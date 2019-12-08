@template(("header",[
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
]))!

<script>hljs.initHighlightingOnLoad();</script>
    <app>
        <div style="display: flex" id="footer_seperator">
            @view(("nav/blog", ["navBlog"=>$blog, "userRank"=>$myRank]))!
            <div class="contents_first">
                <h1 id="post_title">{{ $postTitle }}</h1>
                <p id="post_info">{{$information}}</p>
                <div id="post_user">
                    <img id="post_user_profilepic" src="{{$blog["picture"]}}" />
                    <div>
                        <a id="post_user_name">{{$blog["name"]}}</a>
                        <p id="post_user_description">{{$blog["description"]}}</p>
                    </div>
                </div>
                @if(($image!=null))#
                <img id="post_image" src="{{$image}}" />
                @endif
                <div id="post_contents">
                    {{ $contents }}
                </div>
                <div>
                @if(($myRank !== false))#
                    <br><br><br>
                    <a href="/{{$blog["name"]}}/{{$post["link"]}}/edit" class="btn blue">Edit</a>
                    <a href="/{{$blog["name"]}}/delete/{{$post["id"]}}" class="btn red">Delete</a>
                @endif
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

@template(("footer"))!