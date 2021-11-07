package de.interaapps.quotysco.backend.responses;

import java.util.List;

public class PaginatedListResponse<T> extends ListResponse<T> {
    public Integer page;
    public Integer pageSize;
    public Integer total;
    public PaginatedListResponse(List<T> data){
        super(data);
    }
}
