package de.interaapps.quotysco.backend.controller;

import de.interaapps.quotysco.backend.exceptions.NotFoundException;
import de.interaapps.quotysco.backend.model.*;
import de.interaapps.quotysco.backend.model.Post;
import de.interaapps.quotysco.backend.responses.*;
import org.javawebstack.framework.HttpController;
import org.javawebstack.httpserver.Exchange;
import org.javawebstack.httpserver.router.annotation.*;
import org.javawebstack.orm.Repo;
import org.javawebstack.orm.query.Query;

import java.util.ArrayList;
import java.util.List;
import java.util.stream.Collectors;

@PathPrefix("/api/v1/user")
public class UserController extends HttpController {
    @Get
    @With("auth")
    public UserResponse getUser(@Attrib("session") Session session,  @Attrib("user") User user){
        UserResponse response = new UserResponse();

        boolean getUserPermission = session.hasPermission("user:read");

        if (getUserPermission || session.hasPermission("user.name:read"))
            response.name = user.name;
        if (getUserPermission || session.hasPermission("user.displayname:read"))
            response.displayName = user.displayName;
        if (getUserPermission || session.hasPermission("user.mail:read"))
            response.mail = user.eMail;
        if (getUserPermission || session.hasPermission("user.profilepicture:read"))
            response.profilePicture = user.profilePicture;

        response.success = true;

        return response;
    }

    @Get("/blogs")
    @With("auth")
    public ListResponse<BlogResponse> getMyBlog(@Attrib("session") Session session,  @Attrib("user") User user){
        session.checkPermission("blogs:read");

        return new ListResponse<>(Repo.get(Blog.class)
                .whereExists(BlogUser.class, blogUserQuery -> blogUserQuery.where("userId", user.id).where(Blog.class, "id", "=", BlogUser.class, "blogId"))
                .get().stream().map(BlogResponse::new).collect(Collectors.toList()));
    }

    @Get("/following_posts")
    @With("auth")
    public ListResponse<PostResponse> getFollowingPosts(Exchange exchange, @Attrib("user") User user, @Attrib("session") Session session){
        session.checkPermission("following_blogs:read");

        List<PostResponse> list;

        int limit = 100000;
        int page = 1;

        if (exchange.rawRequest().getParameter("limit") != null)
            limit = Integer.parseInt(exchange.rawRequest().getParameter("limit"));
        if (exchange.rawRequest().getParameter("page") != null)
            page = Integer.parseInt(exchange.rawRequest().getParameter("page"));

        Query<Post> query = Repo.get(Post.class)
                .whereExists(UserBlogFollow.class, userBlogFollowQuery -> userBlogFollowQuery.where("userId", user.id).where(Post.class, "blogId", "=", UserBlogFollow.class, "blogId"))
                .where("state", "PUBLISHED")
                .order("createdAt", true);
        list = query
                .limit(page - 1 < 0 ? 0 : (page - 1) * limit, limit < 0 ? 5 : limit).get()
                .stream()
                .map(post -> new PostResponse(post, null, true, null, true, false, true, exchange.attrib("session")))
                .collect(Collectors.toList());

        PaginatedListResponse<PostResponse> postResponsePaginatedListResponse = new PaginatedListResponse<>(list);

        postResponsePaginatedListResponse.page = page;
        postResponsePaginatedListResponse.pageSize = limit;
        postResponsePaginatedListResponse.total = query.count();

        return postResponsePaginatedListResponse;
    }

    @Get("/top_categories")
    @With("auth")
    public ListResponse<String> getTopCategories(Exchange exchange, @Attrib("user") User user){
        return new ListResponse<>(Repo.get(InterestInteraction.class).where("userId", user.id).order("score", true).limit(exchange.rawRequest().getParameter("limit") == null ? 500 : Integer.parseInt(exchange.rawRequest().getParameter("limit"))).get().stream().map(i->i.category).collect(Collectors.toList()));

    }
}
