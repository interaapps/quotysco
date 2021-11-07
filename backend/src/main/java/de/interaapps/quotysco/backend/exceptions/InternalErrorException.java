package de.interaapps.quotysco.backend.exceptions;

public class InternalErrorException extends RuntimeException {
    public InternalErrorException(){
        super("An internal error occured");
    }
}
