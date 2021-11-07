package de.interaapps.quotysco.backend.controller;

import de.interaapps.quotysco.backend.helper.RequestHelper;
import de.interaapps.quotysco.backend.model.*;
import de.interaapps.quotysco.backend.responses.ListResponse;
import de.interaapps.quotysco.backend.responses.PaginatedListResponse;
import de.interaapps.quotysco.backend.responses.PostResponse;
import org.javawebstack.framework.HttpController;
import org.javawebstack.httpserver.Exchange;
import org.javawebstack.httpserver.router.annotation.Attrib;
import org.javawebstack.httpserver.router.annotation.Get;
import org.javawebstack.httpserver.router.annotation.PathPrefix;
import org.javawebstack.httpserver.router.annotation.With;
import org.javawebstack.orm.Repo;
import org.javawebstack.orm.query.Query;

import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.stream.Collectors;

@PathPrefix("/api/v1/global")
public class HomepageController extends HttpController {
    @Get("/trending")
    public ListResponse<PostResponse> getTrendingPosts(Exchange exchange){
        PaginatedListResponse<PostResponse> pagination = RequestHelper.createPagination(exchange);

        Query<Post> query = Repo.get(Post.class)
                .whereExists(GlobalPostInterestInteraction.class, globalPostInterestInteractionQuery -> globalPostInterestInteractionQuery.where(Post.class, "id", "=", GlobalPostInterestInteraction.class, "postId").order("score",true))
                .order("createdAt", true).where("state", "PUBLISHED");
        if ("true".equals(exchange.rawRequest().getParameter("trending"))) {
            Date date = new Date();
            // Check if post is not over 4 Days old
            date.setTime(System.currentTimeMillis() - (1000 * 60 * 60 * 24 * 4));
            query.where("createdAt", ">", new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(date));
        }
        pagination.data = query
                .limit(pagination.page - 1 < 0 ? 0 : (pagination.page - 1) * pagination.pageSize, pagination.pageSize < 0 ? 5 : pagination.pageSize).get()
                .stream()
                .map(post -> new PostResponse(post, null, true, null, true, false, true, exchange.attrib("session")))
                .collect(Collectors.toList());

        pagination.total = query.count();

        return pagination;
    }

    @Get("/latest")
    public ListResponse<PostResponse> getLatestPosts(Exchange exchange){
        PaginatedListResponse<PostResponse> pagination = RequestHelper.createPagination(exchange);

        Query<Post> query = Repo.get(Post.class).query().order("createdAt", true).where("state", "PUBLISHED");

        pagination.data = query
                .limit(pagination.page - 1 < 0 ? 0 : (pagination.page - 1) * pagination.pageSize, pagination.pageSize < 0 ? 5 : pagination.pageSize).get()
                .stream()
                .map(post -> new PostResponse(post, null, true, null, true, false, true, exchange.attrib("session")))
                .collect(Collectors.toList());

        pagination.total = query.count();

        return pagination;
    }
}
