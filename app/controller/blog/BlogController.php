<?php
namespace app\controller\blog;

use \databases\BlogsTable;
use \databases\PostsTable;
use \databases\UsersFollowingTable;
use \databases\BlogUsersTable;
use ulole\core\classes\util\Str;
use ulole\core\classes\Response;
use \app\classes\User;
use \app\classes\PreventXSS;

class BlogController 
{

    public static function page(){
        global $_ROUTEVAR;
        $blog = (new BlogsTable)
                    ->select("*")
                    ->where("name", $_ROUTEVAR[1])
                    ->first();

        $offset = "";
        if (isset($_GET["page"]) && is_numeric($_GET["page"])) {
            $page = (int) $_GET["page"];
            $offset = ",";
            $offset .= ( 10*$page );
            if ($_GET["page"] <= 0)
                    $offset = "";
        }

        $posts = (new PostsTable)
                    ->select("*")
                    ->where("blogid", $blog["id"])
                    ->order("id DESC")
                    ->limit("10".$offset)
                    ->get();

        if ($blog["id"] == null) return view("error/404");

        view("blog/page", [
            "blog"=>$blog,
            "posts"=>$posts,
            "myRank"=>self::myBlogRole($blog["id"])
        ]);
    }

    public static function newPage() {
        global $_ROUTEVAR;
        if (User::loggedIn()) {
            $blog = (new BlogsTable)
                        ->select("*")
                        ->where("name", $_ROUTEVAR[1])
                        ->first();

            if (self::myBlogRole($blog["id"]) !== null) {
                view("blog/new", [
                    "blog"=>$blog,
                    "myRank"=>self::myBlogRole($blog["id"])
                ]);
            }
        }

    }

    public static function new() {
        global $_ROUTEVAR, $ULOLE_CONFIG_ENV;
        $out = [
            "done"=>false
        ];
        if (User::loggedIn()) {
            $blog = (new BlogsTable)
                        ->select("*")
                        ->where("name", $_ROUTEVAR[1])
                        ->first();

            if (self::myBlogRole($blog["id"]) != null && isset($_POST["contents"]) && isset($_POST["title"])) {
                $post = new PostsTable;
                $post->userid = User::getUserObject()->id;
                $post->blogid = $blog["id"];
                $post->contents = PreventXSS::preventXSS($_POST["contents"]);
                $post->title = \htmlspecialchars($_POST["title"]);
                $post->link = self::checkName(htmlspecialchars($_POST["title"]));
                if (isset($_POST["title"])  && strpos($_POST["file"], $ULOLE_CONFIG_ENV->FTP->url) !== false )
                    $post->image = $_POST["file"];

                $post->type = "POST";
                $post->save();
                $out["done"] = true;
            }
        }
        Response::json($out);

    }

    public static function checkName($name=null, $blog=null) {
        if ($name == null) {
            if (!isset($_POST["title"])) return "Error";
            $name = $_POST["title"];
            global $_ROUTEVAR;
            $blog = (new BlogsTable)
                        ->select("id")
                        ->where("name", $_ROUTEVAR[1])
                        ->first()["id"];
        }

        if ($name == "") $name = "_";
        $name = \strtolower($name);
        $name = (new Str($name))->replace([
            "/"=>"-",
            " "=>"-",
            ":"=>"_"
        ]);
        
        while ((new PostsTable)->select("link")->where("link", $name)->andwhere("blogid", $blog)->first()["link"] !== null)
            $name .= "_";
        

        return $name;
    }

    /**
     * Gets the current users role
     */
    public static function myBlogRole($blog) {
        if (User::loggedIn()) {
            $user = (new BlogUsersTable)
                ->select("rank")
                ->where("blogid", $blog)
                ->andwhere("userid", User::getUserObject()->id)
                ->first();
            if ($user["rank"] !== null)
            return $user["rank"];
        }
        return false;
    }


    /**
     * Checking if current user follows a certain blog
     */
    public static function followed(){
        global $_ROUTEVAR;

        $blog = (new BlogsTable)
                    ->select("*")
                    ->where("name", $_ROUTEVAR[1])
                    ->first();
        if ($blog["id"] !== null) {
            if ( (new UsersFollowingTable)->select("id")->where("userid", User::getUserObject()->id)->andwhere("following", $blog["id"])->first()["id"] !== null )
                return "1";
            return "0";
        }
        return "-1";
    }

    /**
     * Follows a certain blog per RouterVariable
     */
    public static function follow() {
        global $_ROUTEVAR;

        $blog = (new BlogsTable)
                    ->select("*")
                    ->where("name", $_ROUTEVAR[1])
                    ->first();
        if ($blog["id"] !== null) {
            if (self::followed() == "1") {
                (new UsersFollowingTable)->delete()->where("userid", User::getUserObject()->id)->andwhere("following", $blog["id"])->run();
            } else {
                $follow = new UsersFollowingTable;
                $follow->following = $blog["id"];
                $follow->userid = User::getUserObject()->id;
                $follow->save();
            }
        }
    }

}