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

            if (BlogController::myBlogRole($blog["id"]) == "OWNER") {
                (new BlogUsersTable)->delete()->where("userid", $_POST["userid"])->andwhere("blogid", $blog["id"])->run();
                $out["done"] = true;
            }
        }

        Response::json($out);
    }

}