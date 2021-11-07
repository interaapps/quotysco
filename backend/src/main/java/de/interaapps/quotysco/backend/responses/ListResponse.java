package de.interaapps.quotysco.backend.responses;

import java.util.ArrayList;
import java.util.List;

public class ListResponse<T> extends ActionResponse {
    public List<T> data;
    public ListResponse(List<T> data){
        this.data = data;
        success = true;
    }
}
