package de.interaapps.quotysco.backend.exceptions;

public class NotFoundException extends RuntimeException {
    public NotFoundException(){
        super("Page not found");
    }
}
