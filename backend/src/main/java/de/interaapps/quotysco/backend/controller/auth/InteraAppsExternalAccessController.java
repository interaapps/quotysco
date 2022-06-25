package de.interaapps.quotysco.backend.controller.auth;

import de.interaapps.quotysco.backend.QuotyscoBackend;
import de.interaapps.quotysco.backend.exceptions.NotFoundException;
import de.interaapps.quotysco.backend.exceptions.auth.AuthenticationException;
import de.interaapps.quotysco.backend.model.Session;
import de.interaapps.quotysco.backend.model.User;
import de.interaapps.quotysco.backend.requests.auth.InteraAppsExternalAccessRequest;
import org.javawebstack.framework.HttpController;
import org.javawebstack.framework.config.Config;
import org.javawebstack.httpserver.Exchange;
import org.javawebstack.httpserver.router.annotation.Body;
import org.javawebstack.httpserver.router.annotation.PathPrefix;
import org.javawebstack.httpserver.router.annotation.Post;
import org.javawebstack.orm.Repo;

import java.util.Map;

@PathPrefix("/api/v1/auth")
public class InteraAppsExternalAccessController extends HttpController {
    @Post("/iaea")
    public String iaea(@Body InteraAppsExternalAccessRequest request, Exchange exchange){

        if (QuotyscoBackend.getInstance().getIaOAuth2Service() != null) {

            Config config = QuotyscoBackend.getInstance().getConfig();

            if (config.get("ia.oauth2.id").equals(request.appId) && config.get("ia.oauth2.secret").equals(request.appSecret)) {
                User user = Repo.get(User.class).where("oAuthUserId", request.userId).first();

                if (user != null) {
                    Session session = new Session();
                    session.userId = user.id;
                    session.type = Session.Type.ACCESS_TOKEN;
                    request.appScopeList.forEach(session::addScope);

                    session.save();
                    return session.id;
                }
            } else {
                throw new AuthenticationException();
            }
        }

        throw new NotFoundException();
    }
}
