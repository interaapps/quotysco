package de.interaapps.quotysco.backend.exceptions.auth;

public class AuthenticationException extends RuntimeException {
    public AuthenticationException(){
        super("No Access-Token given or invalid. (header: Authorization: Bearer <Token>)");
    }
}
