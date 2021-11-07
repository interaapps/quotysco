package de.interaapps.quotysco.backend.requests;

import org.javawebstack.validator.Rule;

public class BlogEditRequest {
    @Rule("string(3, 50)")
    public String displayName;
    public String description;
    public String website;
    public String layoutType;
    public String image;
}
