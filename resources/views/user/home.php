<?php tmpl("header",["title"=>"Welcome back"]); ?>
    <app>
        <div class="contents_first">

        

            <?php
            $offset = "";
            if (isset($_GET["page"]) && is_numeric($_GET["page"])) {
                $page = (int) $_GET["page"];
                $offset = ",";
                $offset .= ( 10*$page );
                if ($_GET["page"] <= 0)
                    $offset = "";
            }

            $posts = [];
            $blogs = [];

            $table = new \databases\PostsTable;
            foreach ($table->getObject()->query('
            select * 
            from users_following 
            join posts 
            on users_following.following = posts.blogid WHERE users_following.userid = "'.\app\classes\User::getUserObject()->id.'" ORDER BY posts.id DESC LIMIT 10'.$offset.";")
            ->fetchAll() as $obj) {
                array_push($posts, $obj);
            }

            ?>

            <?php if(count($posts) == 0):?>
            
            <img id="welcome_done" src="/assets/images/illustrations/done.svg">
            <h2 class="welcome_h2">Welcome!</h2>
            <p id="welcome_text">
                Welcome to Quotysco!<br>
                Amazing to see you, <span style="color: <?php echo (\app\classes\User::getUserObject()->color); ?>"><?php echo (\app\classes\User::getUserObject()->username); ?></span><br>
                Well, it's a bit clean in here! Decorate it by <a tooltip="Explore" href="/explore">following some nice blogs</a>, or change your <a href="/account/design">design preferences</a>!
            </p>


            <style>
                #welcome_done {
                    display: block;
                    margin: 0px auto;
                    width: 300px;
                    max-width: 80%;
                }

                #welcome_h2 {
                    color: #c80048;
                    font-size: 2.3rem;
                    text-align: center;
                    margin-top: 40px;
                }

                #welcome_text {
                    font-size: 19px;
                }
            </style>
            
            <?php endif; ?>

            <h1 class="center-title">Feed</h1><br><br>

            <?php foreach($posts as $post):?>
                <?php
                if (!isset($blogs[$post["blogid"]]) ) {
                    $blogs[$post["blogid"]] = (new \databases\BlogsTable)->select("*")->where("id", $post["blogid"])->first();
                }
                ?>
                <?php view("components/post", [
                    "post"=>$post,
                    "blog"=>$blogs[$post["blogid"]]
                ]); ?>
            <?php endforeach; ?>

            <?php if(count($posts) >= 10):?>
                <a style="display: inline-table;" class="btn disabled">Last page</a>
                <a href="/?page=<?php echo ( (isset($_GET["page"]) && is_numeric($_GET["page"]) ) ? $_GET["page"]+1  : "2" ); ?>" style="display: inline-table; float: right; margin-left: auto; " class="btn qred">Next page</a>
            <?php else: ?>
                <a href="/?page=<?php echo ( (isset($_GET["page"]) && is_numeric($_GET["page"]) ) ? $_GET["page"]-1  : "" ); ?>" style="display: inline-table;" class="btn qred">Last page</a>
            
                <a style="display: inline-table; float: right; margin-left: auto;" class="btn disabled">Next page</a>
            <?php endif; ?>
        </div>
        <?php
            if (isset($_GET["autotheme"])) echo '<script>setCookie("darktheme", "auto", 1000000);</script>';
        ?>
    </app>

    <script>
    $(".blog_post").click(function(){
        showSnackBar("Opening...", "#d66f1a");
    });
    </script>

<?php tmpl("footer"); ?>