package de.interaapps.quotysco.backend.helper;

import de.interaapps.quotysco.backend.responses.PaginatedListResponse;
import de.interaapps.quotysco.backend.responses.PostResponse;
import org.javawebstack.httpserver.Exchange;

import java.util.ArrayList;
import java.util.List;

public class RequestHelper {
    public static PaginatedListResponse createPagination(Exchange exchange){
        int limit = 100000;
        int page = 1;

        if (exchange.rawRequest().getParameter("limit") != null)
            limit = Integer.parseInt(exchange.rawRequest().getParameter("limit"));
        if (exchange.rawRequest().getParameter("page") != null)
            page = Integer.parseInt(exchange.rawRequest().getParameter("page"));

        PaginatedListResponse<PostResponse> postResponsePaginatedListResponse = new PaginatedListResponse<>(new ArrayList<>());
        postResponsePaginatedListResponse.page = page;
        postResponsePaginatedListResponse.pageSize = limit;

        return postResponsePaginatedListResponse;
    }
}
