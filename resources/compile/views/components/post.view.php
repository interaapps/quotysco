<?#
    global $userList;
    if (!is_array($userList))
        $userList = [];

    if (!isset($userList["user_".$post["userid"]] )){
        $userList["user_".$post["userid"]] = \app\classes\User::findUser([ [
                "id", "=", $post["userid"]
            ] ]);
        }
    $postUser = $userList["user_".$post["userid"]];
    $user = [];
    if ($postUser->userkey != null)
        $user = \app\classes\User::getUserInformation($postUser->userkey);
    $contents = (new \ulole\core\classes\util\Str($post["contents"]))->replace([
        "+++++++YTVID_OPEN+++++++" =>'https://youtube.com/watch?v=',
        "+++++++YTVID_CLOSE+++++++"=>'',
        "+++++++PASTEFY_OPEN+++++++" =>'',
        "+++++++PASTEFY_CLOSE+++++++"=>'',
        '<a '=>"<p ",
        '<a href='=>"<p testattr=",
        '<a>'=>"<p>",
        '</a>'=>"</p>",
    ]);
    $readTime = floor(str_word_count(strip_tags($contents)) / 130);
#?>
    @if(($postUser->userkey != null))#
    <a href="/{{htmlspecialchars($blog["name"])}}/{{$post["link"]}}" class="blog_post ripple">
    <span class="blog_post_date">{{$post["created"]}} - {{ $readTime }} min</span>
        <div class="blog_post_user">
            <img class="blog_post_user_image" src="{{$user->profilepic}}" />
            @if(($blog["type"]=="GROUP"))#
                <img class="blog_post_blog_image" src="{{htmlspecialchars($blog["picture"])}}" />
            @endif
            <span class="blog_post_user_name">{{$user->username}}@if(($blog["type"]=="GROUP"))# @ {{$blog["name"]}}@endif</span>
        </div>
        <h1 class="blog_post_title">{{htmlspecialchars($post["title"])}}</h1>
        @if((htmlspecialchars($post["image"]) != ""))#
        <img class="blog_post_class" id="post_image" src="{{htmlspecialchars($post["image"])}}" />
        @endif
        <div class="blog_post_contents">
            {{\app\classes\PreventXSS::preventXSS($contents)}}                        
        </div>
    </a>
    @endif
