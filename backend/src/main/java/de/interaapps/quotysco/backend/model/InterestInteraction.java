package de.interaapps.quotysco.backend.model;

import org.javawebstack.orm.Model;
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

    @Column(size = 25)
    public String score;

    @Column
    public Timestamp createdAt;

    @Column
    public Timestamp updatedAt;

}
