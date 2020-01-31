<?php tmpl("header",["title"=>htmlspecialchars($blog["name"])."'s Blog"]); ?>
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
                        <a style="display: inline-table; float: right; margin-left: auto;" class="btn green">Follow</a>
                    <?php endif; ?>
                </div>
            </div>        
        </div>


    </app>
<?php tmpl("footer"); ?>