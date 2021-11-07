package de.interaapps.accounts.backend.exceptions.auth;

public class AuthenticationException extends RuntimeException {
    public AuthenticationException(){
        super("No authkey given or invalid. (header: x-auth-key)");
    }
}
