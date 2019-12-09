<?php
namespace app\controller\blog;

use app\classes\User;
use \databases\BlogsTable;
use \databases\PostsTable;
use \databases\CommentsTable;
use ulole\core\classes\util\Str;
use ulole\core\classes\Response;

class CommentsController {

    public static function getComments(){
        global $_ROUTEVAR;
        $blog = (new BlogsTable)
                    ->select("*")
                    ->where("name", $_ROUTEVAR[1])
                    ->first();
        if ($blog["id"] == null) return;
        $post = (new PostsTable)
                ->select('*')
                ->where("link", $_ROUTEVAR[2])
                ->andwhere("blogid", $blog["id"])
                ->first();

        if ($post["id"] == null) return;
        
        $offset = "";
        if (isset($_GET["page"]) && is_numeric($_GET["page"])) {
            $page = (int) $_GET["page"];
            $offset = ",";
            $offset .= ( 10*$page );
            if ($_GET["page"] <= 0)
                $offset = "";
        }

        $out = [];

        foreach ((new CommentsTable)->select("*")->where("postid", $post["id"])->limit("10",$offset)->get() as $obj) {
            $user = User::getUserInformation(User::findUser([ [
                "id", "=", $obj["userid"]
            ]])->userkey);
            array_push($out, [
                "user"=>[
                    "id"=>$user->id,
                    "name"=>$user->username,
                    "pb"=>$user->profilepic
                ],
                "comment"=>$obj
            ]);
        }

        return Response::json($out);
    }

    public static function addComment(){
        global $_ROUTEVAR;
        if (User::loggedIn()) {
            $blog = (new BlogsTable)
                    ->select("*")
                    ->where("name", $_ROUTEVAR[1])
                    ->first();
            if ($blog["id"] == null) return;
            $post = (new PostsTable)
                    ->select('*')
                    ->where("link", $_ROUTEVAR[2])
                    ->andwhere("blogid", $blog["id"])
                    ->first();

            if ($post["id"] == null) return;

            $lastComment = (new CommentsTable)->select("*")
                ->where("userid", User::getUserObject()->id)
                ->andwhere("postid", $post["id"])
                ->order("created DESC")
                ->first();

            if ($lastComment["id"] !== null) {
                $timeStamp = strtotime($lastComment["created"]);
                if ($timeStamp > time()-30 )
                    return "Please dont spam";
            }

            $contents = trim($_POST["contents"]);

            if (\strlen($contents) > 7) {
                $comment = new CommentsTable;
                $comment->postid = $post["id"];
                $comment->userid = User::getUserObject()->id;
                $comment->contents = $contents;
                $comment->save();
                return "Done";
            } else {
                return "Too short";
            }

        }
    }


    public static function removeComment(){
        global $_ROUTEVAR;
        if (User::loggedIn()) {
            $blog = (new BlogsTable)
                    ->select("*")
                    ->where("name", $_ROUTEVAR[1])
                    ->first();
                    echo "DELETE1";
            if ($blog["id"] == null) return;
            $post = (new PostsTable)
                    ->select('*')
                    ->where("link", $_ROUTEVAR[2])
                    ->andwhere("blogid", $blog["id"])
                    ->first();

            if ($post["id"] == null) return;

            $comment = (new CommentsTable)->select("*")
                ->where("id", $_POST["id"])
                ->andwhere("postid", $post["id"])
                ->first();
            if ($comment["id"] == null) return;

            if ($comment["userid"] == User::getUserObject()->id || BlogController::myBlogRole($blog["id"]) !== false) {
                (new CommentsTable)->delete()
                    ->where("id", $_POST["id"])
                    ->andwhere("postid", $post["id"])
                    ->run();
            }
            
        }
    }

}