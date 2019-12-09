@template(("header",["title"=>htmlspecialchars($blog["name"])."'s Blog"]))!
    <app>
        <div style="display: flex" id="footer_seperator">
            @view(("nav/blog", ["navBlog"=>$blog, "userRank"=>$myRank]))!
            <div class="contents_first">
            <input style="display: none" id="filepickeruploadinput" type="file">
                <a style="display: inline-table; float: right; margin-left: auto;" id="save" class="btn disabled">Save</a>
                <br><br><br><br>
                <div id="blog_user">
                    <img id="blog_user_image" src="{{htmlspecialchars($blog["picture"])}}" />
                    <span tooltip="Upload Picture" class="ripple" id="edit_picture">Edit Picture</span>
                    <div id="blog_user_info">
                        <span id="blog_user_name">{{htmlspecialchars($blog["name"])}}</span>
                        <i class="blog_user_editable_indicator material-icons">edit</i><input tooltip="Edit Description" type="text" id="blog_user_description" value="{{htmlspecialchars(str_replace("\"","\\\"",$blog["description"]))}}" ><br>
                        Homepage: <i class="blog_user_editable_indicator material-icons">edit</i><input id="blog_user_homepage" tooltip="Edit Homepage" type="text" value="{{htmlspecialchars(str_replace("\"","\\\"",$blog["homepage"]))}}" >
                    </div>
                    <a tooltip="This is a preview :P" tooltip-pos="bottom" style="display: inline-table; float: right; margin-left: auto;" class="btn disabled">Follow</a>
                    
                </div>


                <div id="posts">
                    <a href="#" class="blog_post ripple">
                        <div class="blog_post_user">
                            <img class="blog_post_user_image" src="https://accounts.interaapps.de/userpbs/JulianFun123.png">
                            <img class="blog_post_blog_image" src="https://interaapps.de/assets/interaapps/icon/icon2.png">
                            <span class="blog_post_user_name">Example @ Example</span>
                        </div>
                        <span class="blog_post_date">2019-12-07 10:00:00 - 0 min</span>
                        <h1 class="blog_post_title">Example</h1>
                        <img class="blog_post_class" id="post_image" src="https://cdn.interaapps.de/service/quotysco/userupload/fb54dcf6499f32e053b0e66c6f826e3682a10baaf53a67fad6aad55d0f61694b96ff26166d5ee1eb1d4da8acaddf2ab03cf20b4f74aa22c1ff73990aa5d7c953ad703482939513a5525c89c4a9c41854.svg">
                        <div class="blog_post_contents"><p>This is an example</p></div>
                    </a>
                    <a href="#" class="blog_post ripple">
                        <div class="blog_post_user">
                            <img class="blog_post_user_image" src="https://accounts.interaapps.de/userpbs/JulianFun123.png">
                            <img class="blog_post_blog_image" src="https://interaapps.de/assets/interaapps/icon/icon2.png">
                            <span class="blog_post_user_name">Example @ Example</span>
                        </div>
                        <span class="blog_post_date">2019-12-07 10:00:00 - 0 min</span>
                        <h1 class="blog_post_title">Example</h1>
                        <div class="blog_post_contents"><p>This is an example without image</p></div>
                    </a>
                </div>


            </div>
        </div>

        <style>
            :root {
                --fav-color: #c80048;
            }

            #edit_picture {
                box-sizing: border-box;
                position: absolute;
                margin-top: 75px;
                background: #00000044;
                cursor: pointer;
                width: 100px;
                padding: 2px;
                text-align: center;
                border-radius: 8px;
            }

            .blog_user_editable_indicator {
                font-size: 14px;
                margin-right: 10px;
                vertical-align: middle;
                text-color: #878787;
            }

            #blog_user_description {
                background: transparent;
                color: #FFFFFF;
                border: none !important;
            }

            #blog_user_homepage {
                background: transparent;
                border: none !important;
                color: #3232EE;
                text-decoration: underlined
            }
            

            #blog_user {
                background: var(--fav-color);
                padding: 15px 25px;
                border-radius: 10px;
                color: #FFFFFF;
                box-shadow: rgba(0, 0, 0, 0.1) 0px; 
                color: #FFFFFF;
            }
        </style>

        <script>

            var appeareance = {
                "fav-color": "#c80048"
            };

            function enableSave() {
                $("#save").removeClass("disabled");
                $("#save").addClass("green");
            }

            $("#blog_user_description").on("change", function(){
                enableSave();
            });

            $("#blog_user_homepage").on("change", function(){
                enableSave();
            });

            $("#filepickeruploadinput").on("change", function(){
                enableSave();
                var FR = new FileReader();
                FR.addEventListener("load", function(e) {
                    if ((e.target.result).includes("data:image/")) 
                        $("#blog_user_image").attr("src", e.target.result);
                }); 
                FR.readAsDataURL( this.files[0] );
                filePicker = this;
            });

            var file = null;
            var filePicker = null;

            function upload(thi, done=function(f){}){
                showSnackBar("Uploading image...", "#d66f1a");
                if (thi.files && thi.files[0]) {   
                    var FR = new FileReader();
                    var files = thi.files;
                    FR.addEventListener("load", function(e) {
                        $("#filepickeruploadpreview").attr("src", "");
                        if ((e.target.result).includes("data:image/")) 
                            $("#filepickeruploadpreview").attr("src", e.target.result);
                        
                        request = {
                            file: e.target.result
                        };
                        
                        if (files[0]) {
                            request.fileName = files[0].name;
                        }
                        Cajax.post("/fileupload/img", request).then(function(resp) {
                            parsedJSONReadFilePicker = JSON.parse(resp.responseText);
                            if (parsedJSONReadFilePicker.done) {
                                file = parsedJSONReadFilePicker.file;
                                showSnackBar("Image uploaded");
                                done(parsedJSONReadFilePicker.file);
                            } 
                        }).send();
                    }); 
                    FR.readAsDataURL( thi.files[0] );
                }
            }

            $("#edit_picture").click(function() {
                $("#filepickeruploadinput").click();
            });

            function save() {
                showSnackBar("Saving...", "#d66f1a");
                Cajax.post("", {
                    picture: file,
                    description: $("#blog_user_description").val(),
                    homepage: $("#blog_user_homepage").val()
                }).then(function(){
                    $("#navigation_bar_profile_picture").attr("src", $("#blog_user_image").attr("src"));
                    showSnackBar("Saved");
                }).send();
            }

            $("#save").click(function(){
                if (filePicker == null) {
                    save();
                } else {
                    upload(filePicker, function(){
                        save();
                    });
                }
            });

        </script>


    </app>
@template(("footer"))!