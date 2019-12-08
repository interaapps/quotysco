<?php
namespace app\controller\blog;

use \databases\BlogsTable;
use \databases\PostsTable;
use ulole\core\classes\util\Str;

class PostController 
{

    public static function page(){
        global $_ROUTEVAR;

        $blog = (new BlogsTable)
                    ->select("*")
                    ->where("name", $_ROUTEVAR[1])
                    ->first();

        if ($blog["id"] == null) return view("error/404");

        $post = (new PostsTable)
                    ->select("*")
                    ->where("link", $_ROUTEVAR[2])
                    ->andwhere("blogid", $blog["id"])
                    ->first();

        if ($post["id"] == null) return view("error/404");

        //<iframe width="100%" src="https://pastefy.ga/api/v1/embed/GxxOj34K" frameborder="0" allowfullscreen></iframe>

        $content = (new Str($post["contents"]))->replace([
            "+++++++YTVID_OPEN+++++++" =>'<iframe class="youtube_player" src="https://www.youtube-nocookie.com/embed/',
            "+++++++YTVID_CLOSE+++++++"=>'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
            "+++++++PASTEFY_OPEN+++++++" =>'<iframe width="100%" src="',
            "+++++++PASTEFY_CLOSE+++++++"=>'"  onload="this.style.height = this.contentWindow.document.body.scrollHeight + \'px\';" frameborder="0" allowfullscreen></iframe>'
        ]);

        
        $readingTime = floor(str_word_count(strip_tags($content)) / 130);
        
        view("blog/post", [
            "postTitle"=>htmlspecialchars($post["title"]),
            "contents"=>$content,
            "information"=>htmlspecialchars($post["created"]." - ".$readingTime." min read"),
            "image"=>htmlspecialchars($post["image"]),
            "post"=>$post,
            "blog"=>[
                "name"=>htmlspecialchars($blog["name"]),
                "picture"=>htmlspecialchars($blog["picture"]),
                "type"=>htmlspecialchars($blog["type"]),
                "description"=>htmlspecialchars($blog["description"])
            ],
            "myRank"=>BlogController::myBlogRole($blog["id"])
        ]);
    }

    public static function delete() {
        global $_ROUTEVAR;
        $post = (new PostsTable)->select("*")->where("id", $_ROUTEVAR[2])->first();
        if ($post["id"] !== null && BlogController::myBlogRole($post["blogid"]) !== false ) {
            (new PostsTable)->delete()->where("id", $_ROUTEVAR[2])->run();
            return '{"done":true}';
        }

        return '{"done":false}';
    }
}