package de.interaapps.quotysco.backend.model;

import org.javawebstack.orm.Model;
import org.javawebstack.orm.Repo;
import org.javawebstack.orm.annotation.Column;
import org.javawebstack.orm.annotation.Dates;

import java.sql.Timestamp;

@Dates
public class InterestInteraction extends Model {
    @Column
    public int id;

    @Column(size = 25)
    public String userId;

    @Column
    public String category;

    @Column
    public int score = 0;

    @Column
    public Timestamp createdAt;

    @Column
    public Timestamp updatedAt;

    public static void addInterestFromPost(Post post, int score, User user){
        new Thread(()->{
            Repo.get(PostCategory.class).where("postId", post.id).get().forEach(postCategory -> {
                InterestInteraction interestInteraction = Repo.get(InterestInteraction.class).where("userId", user.id).where("category", postCategory.category).first();
                if (interestInteraction == null) {
                    interestInteraction = new InterestInteraction();
                    interestInteraction.category = postCategory.category;
                    interestInteraction.userId = user.id;
                }
                interestInteraction.score += score;
                interestInteraction.save();
            });
        }).start();
    }

}
