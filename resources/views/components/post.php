<?php
    if (!isset($userList["user_".$post["userid"]] ))
        $userList["user_".$post["userid"]] = \app\classes\User::findUser([ [
                "id", "=", $post["userid"]
            ] ]);
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
?>
    <?php if($postUser->userkey != null):?>
    <a href="/<?php echo (htmlspecialchars($blog["name"])); ?>/<?php echo ($post["link"]); ?>" class="blog_post ripple">
    <span class="blog_post_date"><?php echo ($post["created"]); ?> - <?php echo ( $readTime ); ?> min</span>
        <div class="blog_post_user">
            <img class="blog_post_user_image" src="<?php echo ($user->profilepic); ?>" />
            <?php if($blog["type"]=="GROUP"):?>
                <img class="blog_post_blog_image" src="<?php echo (htmlspecialchars($blog["picture"])); ?>" />
            <?php endif; ?>
            <span class="blog_post_user_name"><?php echo ($user->username); ?><?php if($blog["type"]=="GROUP"):?> @ <?php echo ($blog["name"]); ?><?php endif; ?></span>
        </div>
        <h1 class="blog_post_title"><?php echo (htmlspecialchars($post["title"])); ?></h1>
        <?php if(htmlspecialchars($post["image"]) != ""):?>
        <img class="blog_post_class" id="post_image" src="<?php echo (htmlspecialchars($post["image"])); ?>" />
        <?php endif; ?>
        <div class="blog_post_contents">
            <?php echo (\app\classes\PreventXSS::preventXSS($contents)); ?>                        
        </div>
    </a>
    <?php endif; ?>
