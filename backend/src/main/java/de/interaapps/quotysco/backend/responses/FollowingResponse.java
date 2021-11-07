package de.interaapps.quotysco.backend.responses;

public class FollowingResponse extends ActionResponse {
    public Boolean following;
    public Integer followers;
    public FollowingResponse(Boolean following) {
        this.following = following;
        success = true;
    }
}
