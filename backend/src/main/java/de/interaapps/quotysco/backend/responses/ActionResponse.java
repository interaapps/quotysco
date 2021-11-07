package de.interaapps.quotysco.backend.responses;

public class ActionResponse {
    public boolean success = false;

    public static ActionResponse error(){
        ActionResponse actionResponse = new ActionResponse();
        actionResponse.success = false;
        return actionResponse;
    }

    public static ActionResponse success(){
        ActionResponse actionResponse = new ActionResponse();
        actionResponse.success = true;
        return actionResponse;
    }
}
