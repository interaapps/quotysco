package de.interaapps.quotysco.backend.requests;

import org.javawebstack.validator.Rule;

public class CreateBlogRequest {
    @Rule("alpha_dash")
    public String name;
}
