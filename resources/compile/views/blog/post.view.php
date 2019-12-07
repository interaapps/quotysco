@template(("header",["title"=>"Post"]))!
<script>hljs.initHighlightingOnLoad();</script>
    <app>
        <div style="display: flex">
            @view(("nav/blog", ["navBlog"=>$blog, "userRank"=>$myRank]))!
            <div class="contents_first">
                <p id="post_info">{{$information}}</p>
                <h1 id="post_title">{{ $postTitle }}</h1>
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