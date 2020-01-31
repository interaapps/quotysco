<?php if($userRank !== null && $userRank !== false):?>
    <div class="navigation_bar">
        <div style="padding: 14px;">
        <img id="navigation_bar_profile_picture" src="<?php echo ($navBlog["picture"]); ?>" style="width: 60px; height: 60px; border-radius: 60px; background: #00000011; padding: 2px; vertical-align: middle;" >
        <a style="vertical-align: middle; margin-left: 10px"><?php echo ($navBlog["name"]); ?></a>
        </div>
        <a class="navigation_bar_link ripple" href="/<?php echo ($navBlog["name"]); ?>">Blog</a>
        <a class="navigation_bar_link ripple" href="/<?php echo ($navBlog["name"]); ?>/a/new">New article</a>
        <!--<a class="navigation_bar_link ripple" href="/<?php echo ($navBlog["name"]); ?>/a/categories">Categories</a>-->
        <?php if($userRank == "OWNER"):?>
            <p>Options</p>
            <?php if($navBlog["type"] == "GROUP"):?>
                <a class="navigation_bar_link" href="/<?php echo ($navBlog["name"]); ?>/a/members">Members</a>
            <?php endif; ?>
            <!--<a class="navigation_bar_link ripple" href="/<?php echo ($navBlog["name"]); ?>/a/general">General</a>-->
            <a class="navigation_bar_link ripple" href="/<?php echo ($navBlog["name"]); ?>/a/appeareance">Appeareance</a>
        <?php endif; ?>
    </div>
    <a id="navbar_open"><i class="material-icons">menu</i></a>
    <style>
        @media screen and (max-width: 720px) {
            #logo {
                left: 70px;
            }

            #navbar_open {
                z-index: 10000000;
                position: fixed;
                top: 10px;
                left: 15px;
                padding: 10px;
                border-radius: 100px;
                cursor: pointer;
            }

            #navbar_open:hover {
                background: #00000022;
            }

            #navbar_open i {
                padding-bottom: 0px;
                display: block;
                user-select: none;
            }
        }
    </style>
    <script>
        $("#navbar_open").click(function(e){
            if (window.innerWidth <= 720) {
                console.log("Baum");
                if ($(".navigation_bar").css("display") == "block")
                    $(".navigation_bar").css("display","");
                else
                    $(".navigation_bar").css("display","block");
            } else
                $(".navigation_bar").css("display","");
            e.stopPropagation();
        });

        $(window).click(function(){
            console.log("Haus");
            if (window.innerWidth <= 720) {
                if ($(".navigation_bar").css("display") == "block")
                    event.preventDefault();

                $(".navigation_bar").css("display","");
            }
        });

        $(".navigation_bar").click(function(e){
            if (window.innerWidth <= 720)
                e.stopPropagation();
        });

    </script>
<?php endif; ?>