package de.interaapps.quotysco.backend.responses;

public class FollowingResponse extends ActionResponse {
    public Boolean following;
    public FollowingResponse(Boolean following) {
        this.following = following;
    }
}
