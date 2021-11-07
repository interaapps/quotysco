package de.interaapps.accounts.backend.exceptions.auth;

public class UserAlreadyExistsException extends RuntimeException {
    public UserAlreadyExistsException(){
        super("Username or E-Mail already used by someone else");
    }
}
