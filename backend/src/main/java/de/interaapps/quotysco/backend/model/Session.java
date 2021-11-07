package de.interaapps.quotysco.backend.model;

import de.interaapps.quotysco.backend.QuotyscoBackend;
import de.interaapps.quotysco.backend.exceptions.PermissionsDeniedException;
import org.apache.commons.lang3.RandomStringUtils;
import org.javawebstack.abstractdata.AbstractArray;
import org.javawebstack.orm.Model;
import org.javawebstack.orm.Repo;
import org.javawebstack.orm.annotation.Column;
import org.javawebstack.orm.annotation.Dates;
import org.javawebstack.passport.Profile;

import java.sql.Timestamp;

@Dates
public class Session extends Model {
    @Column(size = 100)
    public String id;

    @Column(size = 25)
    public String userId;

    @Column
    private Type type = Type.LOGIN;

    @Column
    private AbstractArray scopes;

    @Column
    public Timestamp createdAt;

    @Column
    public Timestamp updatedAt;

    public Session(){
        id = RandomStringUtils.random(100, "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890");
    }

    public User getUser(){
        return Repo.get(User.class).get(userId);
    }

    public boolean hasPermission(String permission){
        if (type == Type.LOGIN || permission.equals(""))
            return true;

        return hasScope(permission) || hasScope(permission.split(":")[0]);
    }

    public Session addScope(String scope){
        if (scopes == null)
            scopes = new AbstractArray();
        scopes.add(scope);
        return this;
    }

    private boolean hasScope(String scope){
        return scopes != null && scopes.stream().anyMatch(scope1 -> scope1.string().equals(scope));
    }

    public boolean checkPermission(String permission){
        if (!hasPermission(permission))
            throw new PermissionsDeniedException();
        return true;
    }

    public enum Type {
        LOGIN,
        API_KEY,
        ACCESS_TOKEN
    }
}
