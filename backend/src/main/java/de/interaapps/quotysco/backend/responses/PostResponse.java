package de.interaapps.quotysco.backend.responses;

import de.interaapps.quotysco.backend.model.Blog;

import java.sql.Timestamp;

public class PostResponse extends ActionResponse {
    public int id;
    public String name;
    public String description;
    public String image;
    public Timestamp createdAt;
    public Timestamp updatedAt;
    public Blog.LayoutType layoutType = Blog.LayoutType.LEFT_NAVIGATION;

    public PostResponse(Blog blog){
        id = blog.id;
        name = blog.name;
        description = blog.description;
        image = blog.image;
        createdAt = blog.createdAt;
        updatedAt = blog.updatedAt;
    }

}
