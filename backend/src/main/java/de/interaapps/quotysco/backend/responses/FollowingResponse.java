package de.interaapps.quotysco.backend.responses;

public class FollowingResponse extends ActionResponse {
    public Boolean following;
    public Integer followerCount;
    public FollowingResponse(Boolean following) {
        this.following = following;
        success = true;
    }
}
