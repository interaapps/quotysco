package de.interaapps.accounts.backend.exceptions.auth;

public class PrivacyPoliciesNotCheckedException extends RuntimeException {
    public PrivacyPoliciesNotCheckedException(){
        super("Accept our privacy policies first");
    }
}
