package de.interaapps.quotysco.backend.controller;

import de.interaapps.quotysco.backend.exceptions.BlogAlreadyExistsException;
import de.interaapps.quotysco.backend.exceptions.NotFoundException;
import de.interaapps.quotysco.backend.exceptions.PermissionsDeniedException;
import de.interaapps.quotysco.backend.model.*;
import de.interaapps.quotysco.backend.model.Post;
import de.interaapps.quotysco.backend.requests.BlogEditRequest;
import de.interaapps.quotysco.backend.requests.BlogUserCreateRequest;
import de.interaapps.quotysco.backend.requests.BlogUserEditRequest;
import de.interaapps.quotysco.backend.requests.CreateBlogRequest;
import de.interaapps.quotysco.backend.responses.*;
import org.javawebstack.framework.HttpController;
import org.javawebstack.httpserver.Exchange;
import org.javawebstack.httpserver.router.annotation.*;
import org.javawebstack.orm.Repo;
import org.javawebstack.orm.query.Query;

import java.util.List;
import java.util.stream.Collectors;

@PathPrefix("/api/v1/blogs")
public class BlogController extends HttpController {

    @org.javawebstack.httpserver.router.annotation.Post
    @With("auth")
    public ActionResponse createBlog(@Body CreateBlogRequest createBlogRequest, @Attrib("user") User user){
        if (Blog.getByName(createBlogRequest.name) == null) {
            Blog blog = new Blog();
            blog.type = Blog.Type.TEAM;
            blog.name = createBlogRequest.name;
            blog.displayName = createBlogRequest.name;
            blog.description = "My awesome new Blog!";
            blog.image = "https://cdn.interaapps.de/service/quotysco/nopb.png";
            blog.save();
            BlogUser blogUser = new BlogUser();
            blogUser.blogId = blog.id;
            blogUser.userId = user.id;
            blogUser.role = BlogUser.Role.OWNER;
            blogUser.save();
            return ActionResponse.success();
        } else
            throw new BlogAlreadyExistsException();
    }

    @Get("/{name}")
    public BlogResponse getBlog(@Path("name") String name, @Attrib("user") User user){
        Blog blog = Blog.getByName(name);
        if (blog == null)
            throw new NotFoundException();
        BlogResponse blogResponse = new BlogResponse(blog).fetchContentInformation();

        if (user != null) {
            blogResponse.following = user.isFollowing(blog);
            blogResponse.memberOf = blog.getUser(user) != null;
        }
        blogResponse.followerCount = blog.getFollowerCount();
        return blogResponse;
    }

    @Put("/{name}")
    @With("auth")
    public ActionResponse updateBlog(@Body BlogEditRequest request, @Path("name") String name, @Attrib("session") Session session, @Attrib("user") User user){
        ActionResponse response = new ActionResponse();
        Blog blog = Blog.getByName(name);

        if (blog != null) {
            BlogUser blogUser = blog.getUser(user);
            if (blogUser != null && (blogUser.role == BlogUser.Role.ADMIN || blogUser.role == BlogUser.Role.OWNER)) {
                boolean blogWritePermission = session.hasPermission("blog:write");


                if (request.displayName != null && (blogWritePermission || session.checkPermission("blog.display_name:write") || session.checkPermission("blog["+blog.id+"].display_name:write")))
                    blog.displayName = request.displayName;

                if (request.website != null && (blogWritePermission || session.checkPermission("blog.website:write") || session.checkPermission("blog["+blog.id+"].website:write")))
                    blog.website = request.website;

                if (request.description != null && (blogWritePermission || session.checkPermission("blog.description:write") || session.checkPermission("blog["+blog.id+"].description:write")))
                    blog.description = request.description;

                if (request.layoutType != null && (blogWritePermission || session.checkPermission("blog.layout_type:write") || session.checkPermission("blog["+blog.id+"].layout_type:write")))
                    blog.layoutType = Blog.LayoutType.valueOf(request.layoutType);

                if (request.image != null && (blogWritePermission || session.checkPermission("blog.image:write") || session.checkPermission("blog["+blog.id+"].image:write")))
                    blog.image = request.image;

                response.success = true;

                blog.save();
            }
        }


        return response;
    }

    @Get("/{name}/posts")
    public ListResponse<PostResponse> getBlogPosts(Exchange exchange, @Path("name") String name, @Attrib("user") User user){
        List<PostResponse> list;
        Blog blog = Blog.getByName(name);
        if (blog == null)
            throw new NotFoundException();

        int limit = 100000;
        int page = 1;

        if (exchange.rawRequest().getParameter("limit") != null)
            limit = Integer.parseInt(exchange.rawRequest().getParameter("limit"));
        if (exchange.rawRequest().getParameter("page") != null)
            page = Integer.parseInt(exchange.rawRequest().getParameter("page"));

        Query<Post> query = Repo.get(Post.class).where("blogId", blog.id);
        query.and(q1->{
            q1.where("state", "PUBLISHED");
            if (user != null && blog.getUser(user) != null)
                q1.orWhere("state", "UNLISTED").orWhere("state", "DRAFT");
            return q1;
        });
        query.order("createdAt", true);

        list = query.limit(page-1 < 0 ? 0 : (page-1) * limit, limit < 0 ? 5 : limit).get()
                .stream()
                .map(post -> new PostResponse(post, blog, false, null, true, false, true, exchange.attrib("session")))
                .collect(Collectors.toList());

        PaginatedListResponse<PostResponse> postResponsePaginatedListResponse = new PaginatedListResponse<>(list);

        postResponsePaginatedListResponse.page = page;
        postResponsePaginatedListResponse.pageSize = limit;
        postResponsePaginatedListResponse.total = query.count();

        return postResponsePaginatedListResponse;
    }

    @org.javawebstack.httpserver.router.annotation.Post("/{name}/follow")
    @With("auth")
    public ActionResponse follow(Exchange exchange, @Path("name") String name, @Attrib("user") User user, @Attrib("session") Session session){
        session.checkPermission("blog:follow");
        Blog blog = Blog.getByName(name);
        if (blog == null)
            throw new NotFoundException();


        List<UserBlogFollow> userBlogFollows = Repo.get(UserBlogFollow.class).where("blogId", blog.id).where("userId", user.id).get();
        if (userBlogFollows.size() > 0) {
            userBlogFollows.forEach(UserBlogFollow::delete);
        } else {
            UserBlogFollow userBlogFollow = new UserBlogFollow();
            userBlogFollow.userId = user.id;
            userBlogFollow.blogId = blog.id;
            userBlogFollow.save();
        }

        return ActionResponse.success();
    }

    @Get("/{name}/members")
    public ListResponse<BlogUserResponse> getMembers(Exchange exchange, @Path("name") String name, @Attrib("user") User user, @Attrib("session") Session session){
        session.checkPermission("blog.members:read");
        Blog blog = Blog.getByName(name);
        if (blog == null)
            throw new NotFoundException();

        return new ListResponse<>(blog.getUsers().stream().map(BlogUserResponse::new).collect(Collectors.toList()));
    }

    @Get("/{name}/members/{userid}")
    public BlogUserResponse getMember(Exchange exchange, @Path("name") String name, @Path("userid") String userId, @Attrib("user") User user, @Attrib("session") Session session){
        session.checkPermission("blog.members:read");
        Blog blog = Blog.getByName(name);
        if (blog == null)
            throw new NotFoundException();
        return new BlogUserResponse(blog.getUser(User.byId(userId)));
    }

    @Delete("/{name}/members/{userid}")
    public ActionResponse removeMember(Exchange exchange, @Path("name") String name, @Path("userid") String userId, @Attrib("user") User user, @Attrib("session") Session session){
        session.checkPermission("blog.members:read");
        Blog blog = Blog.getByName(name);
        if (blog == null)
            throw new NotFoundException();
        BlogUser blogUser = blog.getUser(user);
        if (blogUser != null && (blogUser.role == BlogUser.Role.ADMIN || blogUser.role == BlogUser.Role.OWNER)) {
            blog.getUser(User.byId(userId)).delete();
            return ActionResponse.success();
        } else
            throw new PermissionsDeniedException();
    }

    @Put("/{name}/members/{userid}")
    @With("auth")
    public ActionResponse updateMember(@Body BlogUserEditRequest request, @Path("name") String name, @Path("userid") String userId, @Attrib("user") User user, @Attrib("session") Session session){
        session.checkPermission("blog.members:write");
        Blog blog = Blog.getByName(name);
        if (blog == null)
            throw new NotFoundException();
        if (blog.getUser(user) == null)
            throw new PermissionsDeniedException();

        BlogUser blogUser = blog.getUser(User.byId(userId));
        if (request.role != null)
            blogUser.role = request.role;
        blogUser.save();
        return ActionResponse.success();
    }

    @org.javawebstack.httpserver.router.annotation.Post("/{name}/members")
    @With("auth")
    public ActionResponse addMember(@Body BlogUserCreateRequest request, @Path("name") String name, @Path("userid") String userId, @Attrib("user") User user, @Attrib("session") Session session){
        session.checkPermission("blog.members:write");
        Blog blog = Blog.getByName(name);
        if (blog == null)
            throw new NotFoundException();
        BlogUser blogUser = blog.getUser(user);
        if (blogUser != null && (blogUser.role == BlogUser.Role.ADMIN || blogUser.role == BlogUser.Role.OWNER)) {
            BlogUser newBlogUser = new BlogUser();
            newBlogUser.role = request.role;
            newBlogUser.blogId = blog.id;

            newBlogUser.userId = User.byName(request.name).id;
            newBlogUser.save();

            return ActionResponse.success();
        } else
            throw new PermissionsDeniedException();
    }

    @Delete("/{name}")
    @With("auth")
    public ActionResponse deleteBlog(@Body BlogUserCreateRequest request, @Path("name") String name, @Path("userid") String userId, @Attrib("user") User user, @Attrib("session") Session session){
        session.checkPermission("blog:delete");
        Blog blog = Blog.getByName(name);
        if (blog == null)
            throw new NotFoundException();
        BlogUser blogUser = blog.getUser(user);
        if (blogUser != null && blogUser.role == BlogUser.Role.OWNER) {
            blog.delete();
            return ActionResponse.success();
        } else
            throw new PermissionsDeniedException();
    }


    @Get("/{name}/following")
    @With("auth")
    public ActionResponse following(Exchange exchange, @Path("name") String name, @Attrib("user") User user){
        Blog blog = Blog.getByName(name);
        if (blog == null)
            throw new NotFoundException();

        return new FollowingResponse(user.isFollowing(blog));
    }

}
