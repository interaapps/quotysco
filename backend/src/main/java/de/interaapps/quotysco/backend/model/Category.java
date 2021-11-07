package de.interaapps.quotysco.backend.model;

import org.javawebstack.orm.Model;
import org.javawebstack.orm.annotation.Column;
import org.javawebstack.orm.annotation.Dates;

import java.sql.Timestamp;

@Dates
public class Post extends Model {
    @Column
    public int id;

    @Column
    public String name;

    @Column
    public String image;

    @Column
    public String contents;

    @Column
    public Timestamp createdAt;

    @Column
    public Timestamp updatedAt;
}
