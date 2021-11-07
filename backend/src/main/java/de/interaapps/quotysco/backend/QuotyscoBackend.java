package de.interaapps.quotysco.backend;

import org.javawebstack.command.CommandSystem;
import org.javawebstack.framework.WebApplication;
import org.javawebstack.framework.config.Config;
import org.javawebstack.httpserver.HTTPServer;
import org.javawebstack.orm.ORM;
import org.javawebstack.orm.ORMConfig;
import org.javawebstack.orm.Repo;
import org.javawebstack.orm.exception.ORMConfigurationException;
import org.javawebstack.orm.wrapper.SQL;
import org.javawebstack.passport.OAuth2Module;
import org.javawebstack.passport.services.oauth2.InteraAppsOAuth2Service;
import org.javawebstack.validator.ValidationException;

import java.io.File;
import java.util.HashMap;
import java.util.Map;

public class QuotyscoAccounts extends WebApplication {
    private OAuth2Module oAuth2Module;
    private InteraAppsOAuth2Service iaOAuth2Service;


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
        config.addEnvKeyMapping(envMapping);
        config.addEnvFile(new File(".env"));
    }

    protected void setupModels(SQL sql) throws ORMConfigurationException {
        ORMConfig ormConfig = new ORMConfig().setTablePrefix("quotysco_");
        ORM.register(Session.class.getPackage(), sql, ormConfig);
        ORM.autoMigrate();

    }

    protected void setupServer(HTTPServer server) {
        iaOAuth2Service = new InteraAppsOAuth2Service(getConfig().get("ia.oauth2.id"), getConfig().get("ia.oauth2.secret"), getConfig().get("server.name")).setScopes(new String[]{"user:read"});
        oAuth2Module.addService(iaOAuth2Service);

        oAuth2Module.setOAuth2Callback((s, exchange, oAuth2Callback) -> {
            Session session = new Session();
            session.accessToken = oAuth2Callback.getToken();
            session.refreshToken = oAuth2Callback.getRefreshToken();
            session.save();
            if (exchange.rawRequest().getParameter("popup") != null) {
                SessionResponse response = new SessionResponse();
                response.session = session.id;
                response.success = true;
                return response;
            }
            exchange.redirect("/login.html#"+session.id);
            return "";
        });

        server.exceptionHandler((exchange, throwable) -> {
            if (throwable instanceof ValidationException) {
                ValidationException validationException = (ValidationException) throwable;
                validationException.getResult().getErrorMap().forEach((s, list)->{
                    list.forEach(validationError -> {
                        System.out.println(validationError.getMessage());
                    });
                });
            }
            return "{}";
        });

        server.beforeInterceptor(exchange -> {
            if (exchange.bearerAuth() != null){
                Session session = Repo.get(Session.class).get(exchange.bearerAuth());

                if (session != null) {
                    exchange.attrib("session", session);
                }
            }

            return false;
        });


    }

    protected void setupCommands(CommandSystem commandSystem) {

    }
}
