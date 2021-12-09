package de.interaapps.quotysco.backend.controller.web;

import com.google.gson.Gson;
import de.interaapps.quotysco.backend.QuotyscoBackend;
import de.interaapps.quotysco.backend.model.Blog;
import de.interaapps.quotysco.backend.model.Post;
import de.interaapps.quotysco.backend.requests.PostEditRequest;
import org.apache.commons.lang3.StringEscapeUtils;
import org.javawebstack.abstractdata.AbstractElement;
import org.javawebstack.framework.HttpController;
import org.javawebstack.httpserver.router.annotation.Get;
import org.javawebstack.httpserver.router.annotation.Path;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class PostMetaController extends HttpController {

    private String html = "ERROR";

    {
        InputStream resourceAsStream = getClass().getClassLoader().getResourceAsStream("static/index.html");
        ByteArrayOutputStream byteArrayOutputStream = new ByteArrayOutputStream();

        try {
            byte[] buff = new byte[2323];
            int r;
            while ((r = resourceAsStream.read(buff)) != -1) {
                byteArrayOutputStream.write(buff, 0, r);
            }
        } catch (IOException e) {}
        html = byteArrayOutputStream.toString();
    }

    @Get("/{blogName}/{postName}")
    public String post(@Path("blogName") String blogName, @Path("postName") String postName){
        if (getClass().getClassLoader().getResource("static/"+blogName+"/"+postName) != null)
            return null;
        Map<String, String> tags = new HashMap<>();
        tags.put("og:site_name", "Quotysco");
        tags.put("og:type", "article");
        tags.put("twitter:card", "summary");

        try {
            Blog blog = Blog.getByName(blogName);
            if (blog != null) {
                Post post = Post.getByBlogAndUrl(blog, postName);

                if (post != null) {
                    String blogDisplayName = blog.displayName != null ? blog.displayName : blog.name;
                    String title = post.title+ " | "+blogDisplayName;
                    tags.put("og:title", title);
                    tags.put("title", title);
                    tags.put("article:author", blogDisplayName);
                    if (post.createdAt != null)
                        tags.put("article:published_time", post.createdAt.toString());
                    if (post.updatedAt != null)
                        tags.put("article:article:modified_time", post.updatedAt.toString());

                    String url = QuotyscoBackend.getInstance().getConfig().get("server.name") + "/" + blogName + "/" + postName;
                    tags.put("og:url", url);
                    tags.put("twitter:url", url);
                    if (post.image != null) {
                        tags.put("og:image", post.image);
                        tags.put("twitter:image", post.image);
                        tags.put("twitter:card", "summary_large_image");
                    }

                    List<Map<String, Object>> contents = new Gson().fromJson(post.contents, PostEditRequest.Contents.class).contents;
                    for (Map<String, Object> content : contents) {
                        if ("TEXT".equals(content.get("type"))) {
                            tags.put("og:description", (String) content.get("contents"));
                            tags.put("twitter:description", (String) content.get("contents"));
                            tags.put("description", (String) content.get("contents"));
                            break;
                        }
                    }
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }

        return html.replace("<meta-tags></meta-tags>", tagsToHTML(tags));
    }

    @Get("/{blogName}")
    public String blog(@Path("blogName") String blogName){
        if (getClass().getClassLoader().getResource("static/"+blogName) != null)
            return null;

        Map<String, String> tags = new HashMap<>();
        tags.put("og:site_name", "Quotysco");
        tags.put("og:type", "blog");
        tags.put("twitter:card", "summary");

        try {
            Blog blog = Blog.getByName(blogName);
            if (blog != null) {
                String blogDisplayName = blog.displayName != null ? blog.displayName : blog.name;
                tags.put("og:title", blogDisplayName);
                tags.put("title", blogDisplayName);
                tags.put("og:description", blog.description);
                tags.put("twitter:description", blog.description);
                tags.put("description", blog.description);

                String url = QuotyscoBackend.getInstance().getConfig().get("server.name") + "/" + blogName;
                tags.put("og:url", url);
                tags.put("twitter:url", url);
                if (blog.image != null) {
                    tags.put("og:image", blog.image);
                    tags.put("twitter:image", blog.image);
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }

        return html.replace("<meta-tags></meta-tags>", tagsToHTML(tags));
    }

    private String tagsToHTML(Map<String, String> map){
        StringBuilder out = new StringBuilder();
        map.forEach((key, value) -> {
            String name = StringEscapeUtils.escapeHtml3(key.replace("ä", ""));
            out.append("<meta property=\"").append(name).append("\" name=\"").append(name).append("\" content=\"").append(StringEscapeUtils.escapeHtml3(value)/*.replaceAll("\n", "\\n")*/).append("\" />");
        });
        return out.toString();
    }
}
