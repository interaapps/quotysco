<?php tmpl("header",[
    "title"=>htmlspecialchars($blog["name"])."'s Blog",
    "extrameta"=>'
    <meta property="og:type" content="article" />
    <meta name="og:title" property="og:title" content="'.htmlspecialchars($postTitle).'">
    <meta name="twitter:creator" property="twitter:creator" content="'.htmlspecialchars($blog["name"]).'">
    <meta name="twitter:site" property="twitter:site" content="'.str_replace("\n"," ",substr(strip_tags($contents), 0, 100)).'...">
    <meta property="og:url" content="'.((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]").'" />
    <meta property="og:title" content="'.htmlspecialchars($postTitle).'" />
    <meta property="og:description" content="'.str_replace("\n"," ",substr(strip_tags($contents), 0, 50)).'..." />
    <meta name="Description" content="'.str_replace("\n"," ",substr(strip_tags($contents), 0, 150)).'">
    '.
    ($image != null ?
    '
    <meta name="twitter:image" content="'.htmlspecialchars($image).'">
    <meta property="og:image" content="'.htmlspecialchars($image).'" />
    '
    : "" )
]); ?>

<script>hljs.initHighlightingOnLoad();</script>
    <app>
        <div style="display: flex" id="footer_seperator">
            <?php view("nav/blog", ["navBlog"=>$blog, "userRank"=>$myRank]); ?>
            <div class="contents_first">
                <h1 id="post_title"><?php echo ( $postTitle ); ?></h1>
                <p id="post_info"><?php echo ($information); ?></p>
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
                    <a href="/<?php echo ($blog["name"]); ?>/<?php echo ($post["link"]); ?>/edit" class="btn blue">Edit</a>
                    <a href="/<?php echo ($blog["name"]); ?>/delete/<?php echo ($post["id"]); ?>" class="btn red">Delete</a>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </app>
    
    <!--<div id="rofl">TEST</div> FINSISH: Selection tools

    <script>
    $("#post_contents").on("mouseup", function(){
        if (document.getSelection().toString() !== "") {
            console.log(document.getSelection());
            s = window.getSelection();
            oRange = s.getRangeAt(0);
            oRect = oRange.getBoundingClientRect();
            console.log(document.documentElement.scrollTop+oRect.y);
            $("#rofl").css({
                position: "relative",
                top: (document.documentElement.scrollTop)+"px",
                left: oRect.x+"px"
            });
        }
    });
    </script>-->

<?php tmpl("footer"); ?>