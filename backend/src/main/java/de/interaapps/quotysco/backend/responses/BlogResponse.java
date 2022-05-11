package de.interaapps.quotysco.backend.responses;

import de.interaapps.quotysco.backend.model.Blog;
import de.interaapps.quotysco.backend.model.ContentInformation;
import de.interaapps.quotysco.backend.model.ContentInformationType;
import de.interaapps.quotysco.backend.model.Post;
import org.javawebstack.orm.Repo;

import java.sql.Timestamp;
import java.util.List;

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

    public List<ContentInformationType> contentInformation;

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


    public BlogResponse fetchContentInformation(){
        contentInformation = Repo.get(ContentInformationType.class)
                .whereExists(ContentInformation.class,
                        q -> q.where("contentType", ContentInformation.ContentType.BLOG)
                                .where("contentId", id)
                                .where(ContentInformation.class, "contentInformation", "=", ContentInformationType.class, "id")).get();
        return this;
    }

}
