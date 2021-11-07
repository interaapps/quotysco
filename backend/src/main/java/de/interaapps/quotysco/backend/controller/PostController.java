package de.interaapps.quotysco.backend.controller;

import de.interaapps.quotysco.backend.exceptions.NotFoundException;
import de.interaapps.quotysco.backend.model.Blog;
import de.interaapps.quotysco.backend.model.BlogUser;
import de.interaapps.quotysco.backend.model.Session;
import de.interaapps.quotysco.backend.model.User;
import de.interaapps.quotysco.backend.requests.BlogEditRequest;
import de.interaapps.quotysco.backend.responses.ActionResponse;
import de.interaapps.quotysco.backend.responses.BlogResponse;
import org.javawebstack.framework.HttpController;
import org.javawebstack.httpserver.router.annotation.*;
import org.javawebstack.orm.Repo;

@PathPrefix("/api/v1/blog")
public class BlogController extends HttpController {

    @Get("/{name}")
    public BlogResponse getBlog(@Path("name") String name){
        Blog blog = Repo.get(Blog.class).where("name", name).first();
        if (blog == null)
            throw new NotFoundException();
        return new BlogResponse(blog);
    }

    @Put("/{name}")
    @With("auth")
    public ActionResponse updateBlog(@Body BlogEditRequest request, @Path("name") String name, @Attrib("session") Session session, @Attrib("user") User user){
        ActionResponse response = new ActionResponse();
        Blog blog = Repo.get(Blog.class).where("name", name).first();

        if (blog != null) {
            BlogUser blogUser = blog.getUser(user);
            if (blogUser != null && (blogUser.role == BlogUser.Role.ADMIN || blogUser.role == BlogUser.Role.OWNER)) {
                boolean blogWritePermission = session.hasPermission("blog:write");

                if (request.description != null && (blogWritePermission || session.checkPermission("blog.description:write")))
                    blog.description = request.description;

                if (request.layoutType != null && (blogWritePermission || session.checkPermission("blog.layouttype:write")))
                    blog.layoutType = Blog.LayoutType.valueOf(request.layoutType);

                blog.save();
            }
        }


        return response;
    }

}
