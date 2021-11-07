package de.interaapps.quotysco.backend.model;

import org.javawebstack.orm.Model;
import org.javawebstack.orm.Repo;
import org.javawebstack.orm.annotation.Column;
import org.javawebstack.orm.annotation.Dates;

import java.sql.Timestamp;
import java.util.List;

@Dates
public class Blog extends Model {
    @Column
    public int id;

    @Column(size = 32)
    public String name;

    @Column(size = 40)
    public String displayName = "";

    @Column
    public String description;

    @Column
    public String image;

    @Column
    public boolean verified = false;

    @Column
    public Timestamp createdAt;

    @Column
    public Timestamp updatedAt;

    @Column
    public LayoutType layoutType = LayoutType.LEFT_NAVIGATION;

    @Column
    public Type type;

    @Column
    public String website = "";

    public enum LayoutType {
        LEFT_NAVIGATION,
        TOP_NAVIGATION
    }
    public enum Type {
        USER,
        TEAM
    }

    public Blog(){

    }

    public int addUser(User user, BlogUser.Role role){
        BlogUser blogUser = new BlogUser();
        blogUser.blogId = id;
        blogUser.userId = user.id;
        blogUser.role = role;
        blogUser.save();
        return blogUser.id;
    }

    public List<Post> getPosts(){
        return Repo.get(Post.class).where("blogId", id).get();
    }

    public List<UserBlogFollow> getFollowers(){
        return Repo.get(UserBlogFollow.class).where("blogId", id).get();
    }

    public void delete(){
        getPosts().forEach(Post::delete);
        getUsers().forEach(BlogUser::delete);
        getFollowers().forEach(UserBlogFollow::delete);
        super.delete();
    }

    public BlogUser getUser(User user){
        return Repo.get(BlogUser.class).where("userId", user.id).where("blogId", id).first();
    }

    public List<BlogUser> getUsers(){
        return Repo.get(BlogUser.class).where("blogId", id).get();
    }
    public int getFollowerCount(){
        return Repo.get(UserBlogFollow.class).where("blogId", id).count();
    }

    public static Blog getByName(String name) {
        return Repo.get(Blog.class).where("name", name).first();
    }
}
