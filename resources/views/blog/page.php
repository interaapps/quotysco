<?php tmpl("header",[
    "title"=>htmlspecialchars($blog["name"])."'s Blog",
    "extrameta"=>'
    <meta name="og:title" property="og:title" content="'.htmlspecialchars($blog["description"]).'">
    <meta name="twitter:creator" property="twitter:creator" content="'.htmlspecialchars($blog["name"]).'">
    <meta name="twitter:site" property="twitter:site" content="'.htmlspecialchars($blog["description"]).'">
    <meta property="og:url" content="" />
    <meta property="og:title" content="'.htmlspecialchars($blog["name"]).'" />
    <meta property="og:description" content="'.htmlspecialchars($blog["description"]).'" />
    <meta property="og:image" content="'.htmlspecialchars($blog["picture"]).'" />
    '
]); ?>
    <app>
    <div style="display: flex">
            <?php view("nav/blog", ["navBlog"=>$blog, "userRank"=>$myRank]); ?>
            <div class="contents_first">
                <div id="blog_user">
                    <img id="blog_user_image" src="<?php echo (htmlspecialchars($blog["picture"])); ?>" />
                    <div id="blog_user_info">
                        <span id="blog_user_name"><?php echo (htmlspecialchars($blog["name"])); ?></span>
                        <p id="blog_user_description"><?php echo (htmlspecialchars($blog["description"])); ?></p>
                        Homepage: <a href="<?php echo (htmlspecialchars($blog["homepage"])); ?>"><?php echo (htmlspecialchars($blog["homepage"])); ?></a>
                    </div>
                    <?php if(\app\classes\User::loggedIn()):?>
                        <a style="display: inline-table; float: right; margin-left: auto;" id="follow" class="btn disabled">Follow</a>
                    <?php endif; ?>
                </div>

                <style>
                #blog_user {
                    background: #c80048;
                    padding: 15px 25px;
                    border-radius: 10px;
                    color: #FFFFFF;
                    box-shadow: rgba(0, 0, 0, 0.1) 0px; 
                    color: #FFFFFF;
                }
                #blog_user_name { color: #FFFFFF; }
                #blog_user_description { color: #FFFFFF; }
                </style>

                <?php $userList = []; ?>

                <?php foreach($posts as $post):?>
                    <?php view("components/post", ["post"=>$post, "blog"=>$blog]); ?>
                <?php endforeach; ?>
 
                <?php if(count($posts) >= 10):?>
                <a href="/<?php echo ($blog["name"]); ?>?page=<?php echo ( (isset($_GET["page"]) && is_numeric($_GET["page"]) ) ? $_GET["page"]+1  : "2" ); ?>" style="display: inline-table; float: right; margin-left: auto;" class="btn qred">Next page</a>
                <?php else: ?>
                    <a style="display: inline-table; float: right; margin-left: auto;" class="btn disabled">Next page</a>
                <?php endif; ?>
        
            </div>
        </div>
    </app>

    <script>
        function checkFollowed() {
            $("#follow").text("Loading").addClass("disabled").removeClass("green").removeClass("red-darken-2");
            Cajax.get("/<?php echo ($blog["name"]); ?>/u/following").then(function(res){
                if (res.responseText == "1") {
                    $("#follow").text("Unfollow").addClass("red-darken-2").removeClass("green").removeClass("disabled");
                } else {
                    $("#follow").text("Follow").addClass("green").removeClass("red-darken-2").removeClass("disabled");
                }
            }).send();
        }

        $("#follow").click(function(){
            Cajax.get("/<?php echo ($blog["name"]); ?>/u/follow").then(checkFollowed).send();
        });
        checkFollowed();
    </script>
<?php tmpl("footer"); ?>