@template(("header",[
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
]))!
    <app>
    <div style="display: flex" id="footer_seperator">
            @view(("nav/blog", ["navBlog"=>$blog, "userRank"=>$myRank]))!
            <div class="contents_first">
                <div id="blog_user">
                    <img id="blog_user_image" src="{{htmlspecialchars($blog["picture"])}}" />
                    <div id="blog_user_info">
                        <span id="blog_user_name">{{htmlspecialchars($blog["name"])}}</span>
                        <p id="blog_user_description">{{htmlspecialchars($blog["description"])}}</p>
                        Homepage: <a href="{{htmlspecialchars($blog["homepage"])}}">{{htmlspecialchars($blog["homepage"])}}</a>
                    </div>
                    @if((\app\classes\User::loggedIn()))#
                        <a style="display: inline-table; float: right; margin-left: auto;" id="follow" class="btn disabled">Follow</a>
                    @endif
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

                <?# $userList = []; #?>

                @foreach(($posts as $post))#
                    @view(("components/post", ["post"=>$post, "blog"=>$blog]))!
                @endforeach
 
                @if((count($posts) >= 10))#
                    <a href="/{{$blog["name"]}}?page={{ (isset($_GET["page"]) && is_numeric($_GET["page"]) ) ? $_GET["page"]+1  : "2" }}" style="display: inline-table; float: right; margin-left: auto;" class="btn qred">Next page</a>
                @else
                    <a style="display: inline-table; float: right; margin-left: auto;" class="btn disabled">Next page</a>
                @endif
        
            </div>
        </div>
    </app>

    <script>
        function checkFollowed() {
            $("#follow").text("Loading").addClass("disabled").removeClass("green").removeClass("red-darken-2");
            Cajax.get("/{{$blog["name"]}}/u/following").then(function(res){
                if (res.responseText == "1") {
                    $("#follow").text("Unfollow").addClass("red-darken-2").removeClass("green").removeClass("disabled");
                } else {
                    $("#follow").text("Follow").addClass("green").removeClass("red-darken-2").removeClass("disabled");
                }
            }).send();
        }

        $("#follow").click(function(){
            Cajax.get("/{{$blog["name"]}}/u/follow").then(checkFollowed).send();
        });
        checkFollowed();
    </script>
@template(("footer"))!