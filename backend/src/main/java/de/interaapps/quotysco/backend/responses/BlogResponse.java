package de.interaapps.quotysco.backend.responses;

import de.interaapps.quotysco.backend.model.Blog;
import de.interaapps.quotysco.backend.model.Post;

import java.sql.Timestamp;

public class BlogResponse extends FollowingResponse {
    public int id;
    public String name;
    public String displayName;
    public String description;
    public String image;
    public String website;
    public boolean verified;
    public Blog.Type type;
    public Timestamp createdAt;
    public Timestamp updatedAt;
    public Blog.LayoutType layoutType = Blog.LayoutType.LEFT_NAVIGATION;
    public Boolean memberOf;

    public BlogResponse(Blog blog){
        super(null);
        id = blog.id;
        name = blog.name;
        displayName = blog.displayName;
        description = blog.description;
        image = blog.image;
        type = blog.type;
        verified = blog.verified;
        website = blog.website;
        createdAt = blog.createdAt;
        updatedAt = blog.updatedAt;
        success = true;
    }

}
