<?php
namespace app\controller;

use app\classes\User;
use ulole\core\classes\Response;
use \databases\BlogsTable;
use \databases\BlogUsersTable;
use app\controller\blog\NewBlogController;

class IaAuthController 
{
    public static function login() {
        if (isset($_GET["userkey"])) {
            if (User::getUserInformation($_GET["userkey"]) !== false) {
                $key = User::getUserInformation($_GET["userkey"])->userkey;
                $newUser = new User($key);
                $newUser->login();
                \setcookie("InteraApps_auth", $newUser->session, time()+1593600, "/");
                
                $user = User::getUserInformation($_GET["userkey"]);
                if (json_decode(NewBlogController::checkName("@".$user->username, true))->done) {
                    $blog = new BlogsTable;
                    $blog->name = "@".$user->username;
                    $blog->picture = $user->profilepic;
                    $blog->type = "USER";
                    $blog->homepage = "";
                    $blog->description = $user->description;
                    $blog->save();

                    $blogUser = new BlogUsersTable;
                    $blogUser->blogid = $blog->getObject()->lastInsertId();
                    $blogUser->userid = $user->id;
                    $blogUser->rank = "OWNER";
                    $blogUser->save();
                }

                Response::redirect('/');
            }
        }
    }

    public static function searchUser() {
        global $_POST;
        $out = [
            "done"=>false,
            "errorMessage"=>false,
            "redirect"=>false,
            "user"=>[

            ]
        ];
        
        $users = [];

        $fetch = User::findUser([[
            "name",
            "LIKE",
            "%".$_POST["search"]."%" ] 
        ], 10);

    

        if (isset($fetch->users))
            $users = $fetch->users;

        foreach ($users as $user) {
            $user = User::getUserInformation($user);
            array_push($out["user"], [
                "username"=>$user->username,
                "profileimage"=>$user->profilepic
            ]);
        }

        return Response::returnJson($out);
    }
}