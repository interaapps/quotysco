package de.interaapps.quotysco.backend.controller;

import de.interaapps.quotysco.backend.exceptions.NotFoundException;
import de.interaapps.quotysco.backend.helper.RequestHelper;
import de.interaapps.quotysco.backend.model.Blog;
import de.interaapps.quotysco.backend.model.Category;
import de.interaapps.quotysco.backend.model.Post;
import de.interaapps.quotysco.backend.model.PostCategory;
import de.interaapps.quotysco.backend.responses.CategoryResponse;
import de.interaapps.quotysco.backend.responses.ListResponse;
import de.interaapps.quotysco.backend.responses.PaginatedListResponse;
import de.interaapps.quotysco.backend.responses.PostResponse;
import org.javawebstack.framework.HttpController;
import org.javawebstack.httpserver.Exchange;
import org.javawebstack.httpserver.router.annotation.Get;
import org.javawebstack.httpserver.router.annotation.Path;
import org.javawebstack.httpserver.router.annotation.PathPrefix;
import org.javawebstack.orm.Repo;
import org.javawebstack.orm.query.Query;

import java.util.List;
import java.util.stream.Collectors;

@PathPrefix("/api/v1/categories")
public class CategoryController extends HttpController {

    @Get
    public ListResponse<CategoryResponse> getCategories(){
        return new ListResponse<>(Repo.get(Category.class).all().stream().map(CategoryResponse::new).collect(Collectors.toList()));
    }


    @Get("/{category}")
    public CategoryResponse getCategory(Exchange exchange, @Path("category") String name){
        Category category = Repo.get(Category.class).where("name", name).first();
        if (category == null)
            throw new NotFoundException();
        return new CategoryResponse(category);
    }

    @Get("/{category}/posts")
    public ListResponse<PostResponse> getCategoryPosts(Exchange exchange, @Path("category") String category){
        PaginatedListResponse<PostResponse> pagination = RequestHelper.createPagination(exchange);

        Query<Post> query = Repo.get(Post.class)
                .whereExists(PostCategory.class, categoryQuery -> categoryQuery.where("category", category).where(Post.class, "id", "=", PostCategory.class, "postId"))
                .where("state", "PUBLISHED");
        pagination.data = query
                .limit(pagination.page - 1 < 0 ? 0 : (pagination.page - 1) * pagination.pageSize, pagination.pageSize < 0 ? 5 : pagination.pageSize)
                .order("createdAt", true)
                .get()
                .stream()
                .map(post -> new PostResponse(post, Repo.get(Blog.class).get(post.blogId), false, null, true, false, true, exchange.attrib("session")))
                .collect(Collectors.toList());
        pagination.total = query.count();

        return pagination;
    }
}
