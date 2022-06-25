package de.interaapps.quotysco.backend.requests.auth;

import java.util.List;

public class InteraAppsExternalAccessRequest {
    public String provider;
    public String appId;
    public String appSecret;
    public String scope;

    public List<String> scopeList;
    public List<String> appScopeList;
    public String userId;
}
