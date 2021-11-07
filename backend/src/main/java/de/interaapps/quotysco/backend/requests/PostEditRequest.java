package de.interaapps.quotysco.backend.requests;

import de.interaapps.quotysco.backend.model.Post;
import org.javawebstack.validator.Rule;

import java.util.List;
import java.util.Map;

public class PostEditRequest {
    public String title;
    public String image;
    public Post.State state;
    public Contents contents;

    public String[] categories = new String[]{};


    public static class Contents {
        public int version;

        public List<Map<String, Object>> contents;
    }
}
