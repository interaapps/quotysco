package de.interaapps.quotysco.backend.model;

import org.javawebstack.orm.Model;
import org.javawebstack.orm.annotation.Column;
import org.javawebstack.orm.annotation.Dates;

import java.sql.Timestamp;

public class Category extends Model {
    @Column(id = true, size = 30)
    public String name;

    @Column
    public String displayName;
}
