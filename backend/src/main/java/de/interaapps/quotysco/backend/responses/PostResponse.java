package de.interaapps.quotysco.backend.responses;

import com.google.gson.Gson;
import de.interaapps.quotysco.backend.model.*;
import de.interaapps.quotysco.backend.requests.PostEditRequest;
import org.javawebstack.orm.Repo;

import java.sql.Timestamp;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;

public class PostResponse extends LikedPostResponse {
    public int id;
    public String url;
    public String title;

    public BlogResponse blog;
    public AuthorResponse author;

    public List<CategoryResponse> categories;

    public int likesCount;

    public int commentsCount;

    public Post.State state;

    public Boolean authorIsMe;

    public PostEditRequest.Contents contents;
    public String image;
    public Timestamp createdAt;
    public Timestamp updatedAt;

    public PostResponse(Post post, Blog blog, boolean loadBlog, User user, boolean loadUser, boolean loadCategories, boolean loadLikes, Session currentUser){
        id = post.id;
        url = post.url;
        title = post.title;
        contents = new Gson().fromJson(post.contents, PostEditRequest.Contents.class);
        image = post.image;
        state = post.state;
        createdAt = post.createdAt;
        updatedAt = post.updatedAt;

        if (blog == null && loadBlog)
            this.blog = new BlogResponse(Repo.get(Blog.class).get(post.blogId));
        else if (blog != null)
            this.blog = new BlogResponse(blog);
        if (user == null && loadUser)
            author = new AuthorResponse(Repo.get(User.class).get(post.userId));
        else if (user != null)
            this.author = new AuthorResponse(user);

        if (loadCategories) {
            categories = new ArrayList<>();

            categories = Repo.get(Category.class)
                    .whereExists(PostCategory.class, q1->q1.where("postId", post.id).where(Category.class, "name", "=", PostCategory.class, "category"))
                    .get().stream().map(CategoryResponse::new)
                    .collect(Collectors.toList());
        }

        if (loadLikes) {
            likesCount = Repo.get(UserPostLike.class).where("postId", id).count();

            if (currentUser != null) {
                liked = Repo.get(UserPostLike.class).where("postId", id).where("userId", currentUser.userId).count() > 0;
            }
        }

        if (currentUser != null) {
            authorIsMe = currentUser.userId.equals(post.userId);
        }

        success = true;
    }

}
