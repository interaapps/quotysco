package de.interaapps.quotysco.backend.model;

import org.javawebstack.orm.Model;
import org.javawebstack.orm.Repo;
import org.javawebstack.orm.annotation.Column;
import org.javawebstack.orm.annotation.Dates;

import java.sql.Timestamp;

@Dates
public class GlobalPostInterestInteraction extends Model {
    @Column
    public int id;

    @Column(size = 25)
    public String userId;

    @Column
    public int postId;

    @Column
    public int score = 0;

    @Column
    public Timestamp createdAt;

    @Column
    public Timestamp updatedAt;

    public static void addInterestFromPost(Post post, int score){
        new Thread(()->{
            GlobalPostInterestInteraction interestInteraction = Repo.get(GlobalPostInterestInteraction.class).where("postId", post.id).first();
            if (interestInteraction == null) {
                interestInteraction = new GlobalPostInterestInteraction();
                interestInteraction.postId = post.id;
            }
            interestInteraction.score += score;
            interestInteraction.save();
        }).start();
    }

}
