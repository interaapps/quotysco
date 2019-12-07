@template(("header",[
    "title"=>htmlspecialchars($blog["name"])."'s Blog",
    "extrameta"=>'
    <meta name="og:title" property="og:title" content="'.htmlspecialchars($postTitle).'">
    <meta name="twitter:creator" property="twitter:creator" content="'.htmlspecialchars($blog["name"]).'">
    <meta name="twitter:site" property="twitter:site" content="'.str_replace("\n","\\n",substr(strip_tags($contents), 0, 100)).'...">
    <meta property="og:url" content="'.((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]").'" />
    <meta property="og:title" content="'.htmlspecialchars($postTitle).'" />
    <meta property="og:description" content="'.str_replace("\n","\\n",substr(strip_tags($contents), 0, 50)).'..." />
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
        <div style="display: flex">
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
                    <a href="/{{$blog["name"]}}/delete/{{$post["id"]}}" class="btn red">Delete</a>
                    <a tooltip="Will be added soon!" class="btn disabled">Edit (soon)</a>
                @endif
                </div>
            </div>
        </div>
    </app>

@template(("footer"))!