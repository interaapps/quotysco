@if(($userRank !== null && $userRank !== false))#
    <div class="navigation_bar">
        <div style="padding: 14px;">
        <img id="navigation_bar_profile_picture" src="{{$navBlog["picture"]}}" style="width: 60px; height: 60px; border-radius: 60px; background: #00000011; padding: 2px; vertical-align: middle;" >
        <a style="vertical-align: middle; margin-left: 10px">{{$navBlog["name"]}}</a>
        </div>
        <a class="navigation_bar_link ripple" href="/{{$navBlog["name"]}}">Blog</a>
        <a class="navigation_bar_link ripple" href="/{{$navBlog["name"]}}/a/new">New article</a>
        <!--<a class="navigation_bar_link ripple" href="/{{$navBlog["name"]}}/a/categories">Categories</a>-->
        @if(($userRank == "OWNER"))#
            <p>Options</p>
            @if(($navBlog["type"] == "GROUP"))#
                <!--<a class="navigation_bar_link" href="/{{$navBlog["name"]}}/a/members">Members</a>-->
            @endif
            <!--<a class="navigation_bar_link ripple" href="/{{$navBlog["name"]}}/a/general">General</a>-->
            <a class="navigation_bar_link ripple" href="/{{$navBlog["name"]}}/a/appeareance">Appeareance</a>
        @endif
    </div>
@endif