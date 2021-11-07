package de.interaapps.quotysco.backend.middlewares;

import de.interaapps.quotysco.backend.exceptions.auth.AuthenticationException;
import de.interaapps.quotysco.backend.model.Session;
import de.interaapps.quotysco.backend.model.User;
import org.javawebstack.httpserver.Exchange;
import org.javawebstack.httpserver.handler.RequestHandler;

public class AuthMiddleware implements RequestHandler {
    public Object handle(Exchange exchange) {
        if (exchange.attrib("user") != null) {
            User user = exchange.attrib("user");
            if (user == null)
                throw new AuthenticationException();
            return null;
        }
        throw new AuthenticationException();
    }
}
