package de.interaapps.quotysco.backend.model;

import de.interaapps.quotysco.backend.QuotyscoBackend;
import org.apache.commons.lang3.RandomStringUtils;
import org.javawebstack.orm.Model;
import org.javawebstack.orm.Repo;
import org.javawebstack.orm.annotation.Column;
import org.javawebstack.orm.annotation.Dates;
import org.javawebstack.orm.annotation.SoftDelete;
import org.javawebstack.passport.Profile;

import java.sql.Timestamp;

@SoftDelete
@Dates
public class User extends Model {
    @Column(size = 25)
    public String id;

    @Column(size = 140)
    public String oAuthUserId;

    @Column(size = 32)
    public String name;

    @Column(size = 40)
    public String displayName;

    @Column
    public String eMail;

    @Column
    public String profilePicture;

    @Column(size = 140)
    public String accessToken;

    @Column(size = 140)
    public String refreshToken;

    @Column
    public Timestamp createdAt;

    @Column
    public Timestamp updatedAt;

    @Column
    public Timestamp deletedAt;

    public User(){
        id = RandomStringUtils.random(25, "abcdefghijklmnopqrstuvwxyz1234567890");
    }

    public Profile getUser(){
        return QuotyscoBackend.getInstance().getIaOAuth2Service().getProfile(accessToken);
    }

    public boolean isFollowing(Blog blog){
        return Repo.get(UserBlogFollow.class).where("blogId", blog.id).where("userId", id).first() != null;
    }

    public boolean likedPost(Post post){
        return Repo.get(UserPostLike.class).where("postId", post.id).where("userId", id).first() != null;
    }

    public static User byId(String id){
        return Repo.get(User.class).where("id", id).first();
    }

    public static User byName(String name){
        return Repo.get(User.class).where("name", name).first();
    }
}
