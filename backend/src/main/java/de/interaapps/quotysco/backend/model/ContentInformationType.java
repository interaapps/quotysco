package de.interaapps.quotysco.backend.model;

import org.javawebstack.orm.Model;
import org.javawebstack.orm.annotation.Column;

public class ContentInformationType extends Model {
    @Column
    public int id;

    @Column
    public String title;

    @Column
    public String information;

    @Column
    public String link;

    @Column
    public String logo;

    @Column
    public Level level;

    public enum Level {
        INFORMATION,
        DANGER,
        DONATION
    }
}
