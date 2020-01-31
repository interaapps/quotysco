<?php
namespace app\controller\blog;

use \databases\BlogsTable;
use \databases\PostsTable;
use \databases\BlogUsersTable;
use ulole\core\classes\util\Str;
use ulole\core\classes\Response;
use \app\classes\User;
use \app\classes\PreventXSS;

class MembersController 
{

    public static function membersPage() {
        global $_ROUTEVAR;
        $out = [
            "done"=>false
        ];
        if (User::loggedIn()) {
            $blog = (new BlogsTable)
                        ->select("*")
                        ->where("name", $_ROUTEVAR[1])
                        ->first();

            $members = [];

            foreach ((new BlogUsersTable)->select("*")->where("blogid", $blog["id"])->get() as $member) {
                $user = User::getUserInformation(User::findUser([["id","=",$member["userid"]]])->userkey);
                $user->rank = $member["rank"];
                array_push($members, $user);
            }

            if (BlogController::myBlogRole($blog["id"]) == "OWNER") {
                return view("blog/members", [
                    "blog"=>$blog,
                    "members"=>$members,
                    "myRank"=>BlogController::myBlogRole($blog["id"])
                ]);
            }
        }
    }

    public static function removeUser() {
        global $_ROUTEVAR;
        $out = [
            "done"=>false
        ];
        if (User::loggedIn()) {
            $blog = (new BlogsTable)
                        ->select("*")
                        ->where("name", $_ROUTEVAR[1])
                        ->first();

            if (BlogController::myBlogRole($blog["id"]) == "OWNER" || $_POST["userid"] == User::getUserObject()->id) {
                (new BlogUsersTable)->delete()->where("userid", $_POST["userid"])->andwhere("blogid", $blog["id"])->run();
                $out["done"] = true;
            }
        }

        Response::json($out);
    }

    public static function addUser() {
        global $_ROUTEVAR;
        $blog = (new BlogsTable)
                        ->select("*")
                        ->where("name", $_ROUTEVAR[1])
                        ->first();

        $user = User::getUserInformation(User::findUser([ [
            "name", "=", $_POST["username"]
        ]])->userkey);

        if (BlogController::myBlogRole($blog["id"]) == "OWNER" && BlogController::myBlogRole($blog["id"], $user->id) === false) {
            $blogUser = new BlogUsersTable;
            $blogUser->userid = $user->id;
            $blogUser->blogid = $blog["id"];
            $blogUser->rank = "WRITER";
            $blogUser->save();
            return "true";
        }
        return "false";
    }

    public static function changeUserRank(){
        global $_ROUTEVAR;
        $out = [
            "done"=>false
        ];
        $ranks = [
            "ADMIN",
            "WRITER"
        ];
        
        if (!isset($_POST["userid"]) && !isset($_POST["rank"]) && array_key_exists($_POST["rank"], $ranks))
            return "Invalid Request!";
        
        $blog = (new BlogsTable)
                        ->select("*")
                        ->where("name", $_ROUTEVAR[1])
                        ->first();

        if (BlogController::myBlogRole($blog["id"]) == "OWNER" && BlogController::myBlogRole($blog["id"], $_POST["userid"]) !== false) {
            (new BlogUsersTable)
                ->update()
                ->set("rank", $_POST["rank"])
                ->where("userid", $_POST["userid"])
                ->andwhere("blogid", $blog["id"])
                ->run();

            $out["done"] = true;
        }

        Response::json($out);
    }

}