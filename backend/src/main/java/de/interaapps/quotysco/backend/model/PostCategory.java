package de.interaapps.quotysco.backend.model;

import org.javawebstack.orm.Model;
import org.javawebstack.orm.annotation.Column;
import org.javawebstack.orm.annotation.Dates;

import java.sql.Timestamp;

@Dates
public class PostCategory extends Model {
    @Column
    public int id;

    @Column
    public int postId;

    @Column(size = 30)
    public String category;

    @Column
    public Timestamp createdAt;

    @Column
    public Timestamp updatedAt;
}
