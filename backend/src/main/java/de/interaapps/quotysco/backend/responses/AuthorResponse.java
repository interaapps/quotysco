package de.interaapps.quotysco.backend.responses;

import de.interaapps.quotysco.backend.model.User;

public class AuthorResponse extends ActionResponse {

    public String userId;
    public String name;
    public String displayName;
    public String profilePicture;

    public AuthorResponse(User user){
        userId = user.id;
        name = user.name;
        displayName = user.displayName;
        profilePicture = user.profilePicture;

        success = true;
    }
}
