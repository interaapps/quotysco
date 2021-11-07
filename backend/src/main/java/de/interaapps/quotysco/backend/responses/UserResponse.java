package de.interaapps.quotysco.backend.responses;

import de.interaapps.quotysco.backend.model.User;
import org.javawebstack.abstractdata.AbstractElement;

import java.util.ArrayList;
import java.util.List;

public class UserResponse extends ActionResponse {
    public String id;
    public String name;
    public String profilePicture;
    public String mail;

    public String displayName;

    public static UserResponse fromUser(User user){
        UserResponse userResponse = new UserResponse();
        userResponse.id = user.id;
        userResponse.name = user.name;
        userResponse.profilePicture = user.profilePicture;
        userResponse.mail = user.eMail;
        userResponse.displayName = user.displayName;
        return userResponse;
    }

    public static UserResponse fromUserId(String userId){
        return fromUser(User.byId(userId));
    }
}
