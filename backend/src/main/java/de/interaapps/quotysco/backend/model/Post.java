package de.interaapps.quotysco.backend.model;

import org.javawebstack.orm.Model;
import org.javawebstack.orm.Repo;
import org.javawebstack.orm.annotation.Column;
import org.javawebstack.orm.annotation.Dates;

import java.sql.Timestamp;
import java.util.ArrayList;

@Dates
public class Post extends Model {
    @Column
    public int id;

    @Column(size = 150)
    public String url;

    @Column
    public String title;

    @Column
    public String image;

    @Column
    public String contents;

    @Column(size = 25)
    public String userId;

    @Column
    public int blogId;

    @Column
    public State state;

    @Column
    public Timestamp createdAt;

    @Column
    public Timestamp updatedAt;


    public static Post getByBlogAndUrl(Blog blog, String url){
        return Repo.get(Post.class).where("blogId", blog.id).where("url", url).and(q->q.where("state", State.PUBLISHED).orWhere("state", State.UNLISTED)).first();
    }

    public static Post getByBlogAndUrl(String blogName, String url){
        Blog blog = Blog.getByName(blogName);
        return blog != null ? getByBlogAndUrl(blog, url) : null;
    }

    public enum State {
        DRAFT,
        UNLISTED,
        PUBLISHED
    }

    public void delete(){
        Repo.get(PostCategory.class).where("postId", id).delete();
        Repo.get(UserPostLike.class).where("postId", id).delete();
        Repo.get(GlobalPostInterestInteraction.class).where("postId", id).delete();
        super.delete();
    }
}
