package de.interaapps.quotysco.backend.exceptions;

public class PermissionsDeniedException extends RuntimeException {
    public PermissionsDeniedException(){
        super("You haven't got the permission to this resource!");
    }
}
