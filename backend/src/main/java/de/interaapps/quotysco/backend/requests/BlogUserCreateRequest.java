package de.interaapps.quotysco.backend.requests;

import de.interaapps.quotysco.backend.model.BlogUser;

public class BlogUserCreateRequest {
    public String name;
    public BlogUser.Role role = BlogUser.Role.WRITER;
}
