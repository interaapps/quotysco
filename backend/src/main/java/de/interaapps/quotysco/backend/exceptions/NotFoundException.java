package de.interaapps.accounts.backend.exceptions;

public class NotFoundException extends RuntimeException {
    public NotFoundException(){
        super("Page not found");
    }
}
