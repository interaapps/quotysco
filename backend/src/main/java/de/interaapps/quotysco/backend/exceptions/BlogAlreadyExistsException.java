package de.interaapps.quotysco.backend.exceptions;

public class BlogAlreadyExistsException extends RuntimeException {
    public BlogAlreadyExistsException(){
        super("Blog already exists!");
    }
}
