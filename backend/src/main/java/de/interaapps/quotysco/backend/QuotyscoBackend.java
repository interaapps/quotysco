package de.interaapps.quotysco.backend;

import de.interaapps.quotysco.backend.controller.UserController;
import de.interaapps.quotysco.backend.middlewares.AuthMiddleware;
import de.interaapps.quotysco.backend.model.Blog;
import de.interaapps.quotysco.backend.model.BlogUser;
import de.interaapps.quotysco.backend.model.Session;
import de.interaapps.quotysco.backend.model.User;
import de.interaapps.quotysco.backend.responses.ActionResponse;
import de.interaapps.quotysco.backend.responses.SessionResponse;
import org.apache.commons.net.ftp.FTPClient;
import org.javawebstack.command.CommandSystem;
import org.javawebstack.framework.HttpController;
import org.javawebstack.framework.WebApplication;
import org.javawebstack.framework.config.Config;
import org.javawebstack.httpserver.HTTPServer;
import org.javawebstack.httpserver.handler.RequestHandler;
import org.javawebstack.orm.ORM;
import org.javawebstack.orm.ORMConfig;
import org.javawebstack.orm.Repo;
import org.javawebstack.orm.exception.ORMConfigurationException;
import org.javawebstack.orm.wrapper.SQL;
import org.javawebstack.passport.OAuth2Module;
import org.javawebstack.passport.services.oauth2.InteraAppsOAuth2Service;
import org.javawebstack.validator.ValidationException;

import java.io.File;
import java.io.IOException;
import java.util.HashMap;
import java.util.Map;

public class QuotyscoBackend extends WebApplication {
    private OAuth2Module oAuth2Module;
    private InteraAppsOAuth2Service iaOAuth2Service;
    private static QuotyscoBackend instance;
    private FTPClient resourcesServer;

    protected void setupConfig(Config config) {
        Map<String, String> envMapping = new HashMap<>();
        envMapping.put("SERVER_NAME", "server.name");
        envMapping.put("OAUTH_GOOGLE_ID", "oauth.google.id");
        envMapping.put("OAUTH_GOOGLE_SECRET", "oauth.google.secret");
        envMapping.put("OAUTH_GITHUB_ID", "oauth.github.id");
        envMapping.put("OAUTH_GITHUB_SECRET", "oauth.github.secret");
        envMapping.put("FTP_URL", "ftp.url");
        envMapping.put("FTP_HOST", "ftp.host");
        envMapping.put("FTP_USER", "ftp.user");
        envMapping.put("FTP_PASSWORD", "ftp.password");
        envMapping.put("FTP_BASE_DIR", "ftp.basedir");
        envMapping.put("IA_OAUTH2_CLIENT_ID", "ia.oauth2.id");
        envMapping.put("IA_OAUTH2_CLIENT_SECRET", "ia.oauth2.secret");
        config.addEnvKeyMapping(envMapping);
        config.addEnvFile(new File(".env"));

        iaOAuth2Service = new InteraAppsOAuth2Service(config.get("ia.oauth2.id"), config.get("ia.oauth2.secret"), getConfig().get("server.name")).setScopes(new String[]{"user:read"});
        oAuth2Module.addService(iaOAuth2Service);
    }

    protected void setupModels(SQL sql) throws ORMConfigurationException {
        ORMConfig ormConfig = new ORMConfig().setTablePrefix("quotysco_new_");
        ORM.register(Session.class.getPackage(), sql, ormConfig);
        ORM.autoMigrate();

    }

    protected void setupServer(HTTPServer server) {
        oAuth2Module.setOAuth2Callback((s, exchange, oAuth2Callback) -> {
            Session session = new Session();

            User user = Repo.get(User.class).where("oAuthUserId", oAuth2Callback.getProfile().id).first();
            if (user == null) {
                user = new User();
                user.name = oAuth2Callback.getProfile().name;
                Blog blog = new Blog();
                blog.name = "@"+oAuth2Callback.getProfile().name;
                blog.displayName = oAuth2Callback.getProfile().name;
                blog.description = "My new awesome personal Blog!";
                blog.image = oAuth2Callback.getProfile().avatar;
                blog.layoutType = Blog.LayoutType.TOP_NAVIGATION;
                blog.type = Blog.Type.USER;
                blog.save();
                blog.addUser(user, BlogUser.Role.OWNER);
            }

            user.displayName = oAuth2Callback.getProfile().get("full_name").string();
            user.eMail = oAuth2Callback.getProfile().getMail();
            user.profilePicture = oAuth2Callback.getProfile().getAvatar();
            user.accessToken = oAuth2Callback.getToken();
            user.refreshToken = oAuth2Callback.getRefreshToken();
            user.oAuthUserId = oAuth2Callback.getProfile().getId();

            user.save();
            session.userId = user.id;
            session.save();

            if (exchange.rawRequest().getParameter("popup") != null) {
                SessionResponse response = new SessionResponse();
                response.session = session.id;
                response.success = true;
                return response;
            }
            exchange.redirect("/oauthlogin#"+session.id);
            return "";
        });

        server.exceptionHandler((exchange, throwable) -> {
            throwable.printStackTrace();
            if (throwable instanceof ValidationException) {
                ValidationException validationException = (ValidationException) throwable;
                validationException.getResult().getErrorMap().forEach((s, list)->{
                    list.forEach(validationError -> {
                        System.out.println(validationError.getMessage());
                    });
                });
            }
            exchange.status(500);
            return new ActionResponse();
        });

        server.beforeInterceptor(exchange -> {
            if (exchange.bearerAuth() != null){
                Session session = Repo.get(Session.class).get(exchange.bearerAuth());

                if (session != null) {
                    exchange.attrib("session", session);
                    User user = session.getUser();
                    if (user != null)
                        exchange.attrib("user", user);
                }
            }

            return false;
        });

        server.middleware("auth", new AuthMiddleware());
        server.controller(HttpController.class, UserController.class.getPackage());

        RequestHandler requestHandler = exchange -> {
            try {
                exchange.write(getClass().getClassLoader().getResourceAsStream("static/index.html"));
            } catch (IOException e) {
                e.printStackTrace();
            }
            return "";
        };
        server.get("/", requestHandler);
        server.staticResourceDirectory("/", "static");
        server.get("/{*:path}", requestHandler);
    }

    protected void setupCommands(CommandSystem commandSystem) {

    }

    protected void setupModules() {
        oAuth2Module = new OAuth2Module();
        addModule(oAuth2Module);
    }

    public static QuotyscoBackend getInstance() {
        return instance;
    }

    public InteraAppsOAuth2Service getIaOAuth2Service() {
        return iaOAuth2Service;
    }

    public static void main(String[] args) {
        instance = new QuotyscoBackend();
        instance.start();
    }

    public FTPClient getResourcesServer() {
        try {
            if (resourcesServer == null || !resourcesServer.isConnected() || !resourcesServer.sendNoOp()) {
                resourcesServer = new FTPClient();
                resourcesServer.connect(getConfig().get("ftp.host"));

                System.out.println("CONNECTING");

                resourcesServer.login(getConfig().get("ftp.user"), getConfig().get("ftp.password"));
                resourcesServer.enterLocalPassiveMode();
                resourcesServer.pasv();
                resourcesServer.changeWorkingDirectory(getConfig().get("ftp.basedir", "ia"));
            }
        } catch (IOException e) {
            e.printStackTrace();
            resourcesServer = null;
        }
        return resourcesServer;
    }


}
