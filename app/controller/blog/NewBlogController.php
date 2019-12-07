<?php
namespace app\controller\blog;

use \databases\BlogsTable;
use \databases\PostsTable;
use \databases\BlogUsersTable;
use ulole\core\classes\util\Str;
use ulole\core\classes\Response;
use \app\classes\User;
use \app\classes\PreventXSS;

class NewBlogController 
{

    public static function page(){
        if (User::loggedIn()) {
            view("landing/newblog");
        } else Response::redirect("/");
    }

    public static function createBlog(){
        $out = ["done"=>false, "error"=>"Internal", "redirect"=>"/"];
        if (isset($_POST["name"])) {
            $check = json_decode(self::checkName($_POST["name"]));
            if ($check->done) {

                $blog = new BlogsTable;
                $blog->name = $_POST["name"];
                $blog->picture = "https://accounts.interaapps.de/nopb.png";
                $blog->type = "GROUP";
                $blog->homepage = "";
                $blog->description = "Welcome to my Blog";
                $blog->save();
                $out["redirect"] = $blog->name;
                $blogUser = new BlogUsersTable;
                $blogUser->blogid = $blog->getObject()->lastInsertId();
                $blogUser->userid = User::getUserObject()->id;
                $blogUser->rank = "OWNER";
                $blogUser->save();
                $out["done"] = true;
            } else $out["error"] = $check->error;
        }
        return Response::json($out);
    }

    public static function checkName($name=false, $ignorePregMatch=false) {
        $out = ["done"=>false, "error"=>"Internal"];
        if ($name == false) {
            $name = $_POST["name"];
        }

        if ((new BlogsTable)->select("*")->where("name", $name)->first()["id"] == null) {
            $out["done"] = true;
        } else $out["error"] = "This name is already taken!";

        if (!(preg_match('#^[A-Za-z0-9_]+$#', $name) &&
                $name != "" && 
                !$ignorePregMatch && strlen($name) >= 3 && 
                strlen($name) <= 30)
        ){
            $out["done"] = false;
            $out["error"] = "The name can only contains numbers, letters and underscores. Minimum 3 letters and maximum 30";
        }

        if ( count((new BlogUsersTable)->select("id")->where("userid", User::getUserObject()->id)->get()) >= 10 ) {
            $out["done"] = false;
            $out["error"] = "You have reached the maximum of Blogs! Contact support@interaapps.de";
        }
        
        return Response::returnJson($out);
    }

}