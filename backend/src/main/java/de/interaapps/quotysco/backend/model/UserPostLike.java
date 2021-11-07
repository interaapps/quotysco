package de.interaapps.quotysco.backend.model;

import org.javawebstack.orm.Model;
import org.javawebstack.orm.annotation.Column;
import org.javawebstack.orm.annotation.Dates;

import java.sql.Timestamp;

@Dates
public class UserPostLike extends Model {
    @Column
    public int id;

    @Column
    public int postId;

    @Column(size = 25)
    public String userId;

    @Column
    public Timestamp createdAt;

    @Column
    public Timestamp updatedAt;
}
