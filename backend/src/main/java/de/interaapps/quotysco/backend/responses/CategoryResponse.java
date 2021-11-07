package de.interaapps.quotysco.backend.responses;

import de.interaapps.quotysco.backend.model.*;

public class CategoryResponse extends LikedPostResponse {
    public String name;
    public String displayName;


    public CategoryResponse(Category category){
        name = category.name;
        displayName = category.displayName;
        success = true;
    }
}
