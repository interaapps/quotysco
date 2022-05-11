package de.interaapps.quotysco.backend.model;

import org.javawebstack.orm.Model;
import org.javawebstack.orm.Repo;
import org.javawebstack.orm.annotation.Column;
import org.javawebstack.orm.annotation.Dates;

import java.sql.Timestamp;

@Dates
public class ContentInformation extends Model {
    @Column
    public int id;

    @Column
    public String contentId;

    @Column
    public ContentType contentType;

    @Column
    public int contentInformation;

    @Column
    public Timestamp createdAt;

    @Column
    public Timestamp updatedAt;

    public ContentInformationType getContentInformationType(){
        return Repo.get(ContentInformationType.class).get(contentInformation);
    }

    public enum ContentType {
        POST,
        BLOG,
        CATEGORY
    }
}
