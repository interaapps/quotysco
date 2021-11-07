package de.interaapps.quotysco.backend.controller;

import com.google.gson.Gson;
import de.interaapps.quotysco.backend.QuotyscoBackend;
import de.interaapps.quotysco.backend.exceptions.InvalidRequestException;
import de.interaapps.quotysco.backend.exceptions.NotFoundException;
import de.interaapps.quotysco.backend.exceptions.PermissionsDeniedException;
import de.interaapps.quotysco.backend.model.*;
import de.interaapps.quotysco.backend.model.Post;
import de.interaapps.quotysco.backend.requests.BlogEditRequest;
import de.interaapps.quotysco.backend.requests.PostEditRequest;
import de.interaapps.quotysco.backend.responses.*;
import org.javawebstack.abstractdata.AbstractElement;
import org.javawebstack.framework.HttpController;
import org.javawebstack.httpserver.Exchange;
import org.javawebstack.httpserver.router.annotation.*;
import org.javawebstack.orm.Repo;
import org.javawebstack.validator.ValidationContext;
import org.javawebstack.validator.ValidationException;
import org.javawebstack.validator.Validator;
import org.javawebstack.validator.rule.AlphaDashRule;

@PathPrefix("/api/v1/posts")
public class PostController extends HttpController {

    @Get("/{blog}/{name}")
    public PostResponse getBlog(Exchange exchange, @Path("blog") String blogName, @Path("name") String name, @Attrib("user") User user){
        Blog blog = Repo.get(Blog.class).where("name", blogName).first();
        if (blog == null)
            throw new NotFoundException();
        Post post = Repo.get(Post.class).where("url", name).where("blogId", blog.id).first();
        if (post == null)
            throw new NotFoundException();

        PostResponse postResponse = new PostResponse(post, blog, false, null, true, true, true, exchange.attrib("session"));
        if (user != null) {
            postResponse.blog.memberOf = blog.getUser(user) != null;

            InterestInteraction.addInterestFromPost(post, 5, user);
        }
        GlobalPostInterestInteraction.addInterestFromPost(post, user == null ? 5 : 10);
        return postResponse;
    }

    @Put("/{blog}/{name}")
    @With("auth")
    public ActionResponse updatePost(@Body PostEditRequest request, @Path("blog") String blogName, @Path("name") String name, @Attrib("session") Session session, @Attrib("user") User user){
        ActionResponse response = new ActionResponse();
        Blog blog = Repo.get(Blog.class).where("name", blogName).first();

        String result = new AlphaDashRule().validate(null, null, AbstractElement.fromAbstractObject(name));

        if (result != null)
            throw new InvalidRequestException(result);

        if (blog != null) {
            BlogUser blogUser = blog.getUser(user);
            if (blogUser != null) {
                session.checkPermission("post:write");
                Post post = Repo.get(Post.class).where("url", name).where("blogId", blog.id).first();
                if (post == null) {
                    post = new Post();
                    post.blogId = blog.id;
                    post.userId = user.id;
                    post.url = name;
                }

                post.title = request.title;
                post.image = request.image;
                if (post.state == null && request.state == null)
                    post.state = Post.State.PUBLISHED;
                else if (request.state != null)
                    post.state = request.state;
                post.contents = new Gson().toJson(request.contents);


                if (!post.userId.equals(session.userId))
                    throw new PermissionsDeniedException();

                post.save();

                Repo.get(PostCategory.class).where("postId", post.id).delete();
                for (CategoryResponse category : request.categories) {
                    PostCategory postCategory = new PostCategory();
                    postCategory.category = category.name;
                    postCategory.postId   = post.id;
                    postCategory.save();
                }

                response.success = true;
            }
        }


        return response;
    }

    @Delete("/{blog}/{name}")
    @With("auth")
    public ActionResponse deletePost(@Body PostEditRequest request, @Path("blog") String blogName, @Path("name") String name, @Attrib("session") Session session, @Attrib("user") User user){
        ActionResponse response = new ActionResponse();
        Blog blog = Repo.get(Blog.class).where("name", blogName).first();

        String result = new AlphaDashRule().validate(null, null, AbstractElement.fromAbstractObject(name));

        if (result != null)
            throw new InvalidRequestException(result);

        if (blog != null) {
            if (blog.getUser(user) != null) {
                session.checkPermission("post:write");
                Post post = Repo.get(Post.class).where("url", name).where("blogId", blog.id).first();
                if (post == null)
                    throw new NotFoundException();
                post.delete();
                response.success = true;
            }
        }

        return response;
    }


    @org.javawebstack.httpserver.router.annotation.Post("/{blog}/{name}/like")
    @With("auth")
    public ActionResponse likePost(@Path("blog") String blogName, @Path("name") String name, @Attrib("session") Session session, @Attrib("user") User user){
        ActionResponse response = new ActionResponse();
        Blog blog = Repo.get(Blog.class).where("name", blogName).first();

        if (blog != null) {
            session.checkPermission("post:like");
            Post post = Repo.get(Post.class).where("url", name).where("blogId", blog.id).first();
            if (post != null) {
                UserPostLike userPostLike = Repo.get(UserPostLike.class).where("postId", post.id).where("userId", user.id).first();
                if (userPostLike == null) {
                    userPostLike = new UserPostLike();
                    userPostLike.postId = post.id;
                    userPostLike.userId = user.id;
                    userPostLike.save();

                    InterestInteraction.addInterestFromPost(post, 50, user);
                    GlobalPostInterestInteraction.addInterestFromPost(post, 100);
                } else {
                    userPostLike.delete();
                    InterestInteraction.addInterestFromPost(post, -50, user);
                    GlobalPostInterestInteraction.addInterestFromPost(post, -100);
                }
                response.success = true;
            } else
                throw new NotFoundException();
        }


        return response;
    }

    @Get("/{blog}/{name}/liked")
    @With("auth")
    public LikedPostResponse likedPost(@Path("blog") String blogName, @Path("name") String name, @Attrib("session") Session session, @Attrib("user") User user){
        LikedPostResponse response = new LikedPostResponse();
        Blog blog = Repo.get(Blog.class).where("name", blogName).first();

        if (blog != null) {
            session.checkPermission("post:like");
            Post post = Repo.get(Post.class).where("url", name).where("blogId", blog.id).first();
            if (post != null) {
                response.liked = user.likedPost(post);
                response.success = true;
            } else
                throw new NotFoundException();
        }

        return response;
    }
}
