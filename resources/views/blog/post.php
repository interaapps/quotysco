<?php tmpl("header",["title"=>"Post"]); ?>
<script>hljs.initHighlightingOnLoad();</script>
    <app>
        <div style="display: flex">
            <?php view("nav/blog", ["navBlog"=>$blog, "userRank"=>$myRank]); ?>
            <div class="contents_first">
                <p id="post_info"><?php echo ($information); ?></p>
                <h1 id="post_title"><?php echo ( $postTitle ); ?></h1>
                <div id="post_user">
                    <img id="post_user_profilepic" src="<?php echo ($blog["picture"]); ?>" />
                    <div>
                        <a id="post_user_name"><?php echo ($blog["name"]); ?></a>
                        <p id="post_user_description"><?php echo ($blog["description"]); ?></p>
                    </div>
                </div>
                <?php if($image!=null):?>
                <img id="post_image" src="<?php echo ($image); ?>" />
                <?php endif; ?>
                <div id="post_contents">
                    <?php echo ( $contents ); ?>
                </div>
                <div>
                <?php if($myRank !== false):?>
                    <br><br><br>
                    <a href="/<?php echo ($blog["name"]); ?>/delete/<?php echo ($post["id"]); ?>" class="btn red">Delete</a>
                    <a tooltip="Will be added soon!" class="btn disabled">Edit (soon)</a>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </app>

<?php tmpl("footer"); ?>