package de.interaapps.accounts.backend.exceptions.auth;

public class AuthenticationInvalidException extends RuntimeException {
    public AuthenticationInvalidException(){
        super("Account not found or password is incorrect");
    }
}
