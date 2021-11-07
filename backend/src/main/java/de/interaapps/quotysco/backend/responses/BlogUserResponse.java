package de.interaapps.quotysco.backend.responses;

import de.interaapps.quotysco.backend.model.Blog;
import de.interaapps.quotysco.backend.model.BlogUser;
import org.javawebstack.orm.annotation.Column;

import java.sql.Timestamp;

public class BlogUserResponse extends ActionResponse {
    public BlogUser.Role role;
    public UserResponse user;

    public BlogUserResponse(BlogUser blogUser){
        role = blogUser.role;
        user = UserResponse.fromUserId(blogUser.userId);
        user.mail = null;
    }

}
