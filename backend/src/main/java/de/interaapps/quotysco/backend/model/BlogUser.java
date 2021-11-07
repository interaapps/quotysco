package de.interaapps.quotysco.backend.model;

import org.javawebstack.orm.Model;
import org.javawebstack.orm.annotation.Column;
import org.javawebstack.orm.annotation.Dates;

import java.sql.Timestamp;

@Dates
public class Blog extends Model {
    @Column
    public int id;

    @Column
    public String name;

    @Column
    public String description;

    @Column
    public String image;

    @Column
    public Timestamp createdAt;

    @Column
    public Timestamp updatedAt;
}
