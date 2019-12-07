<?php
namespace app\controller\blog;

use \databases\BlogsTable;
use \databases\PostsTable;
use \databases\BlogUsersTable;
use ulole\core\classes\util\Str;
use ulole\core\classes\Response;
use \app\classes\User;
use \app\classes\PreventXSS;

class AppeareanceController 
{

    public static function page() {
        global $_ROUTEVAR;
        if (User::loggedIn()) {
            $blog = (new BlogsTable)
                        ->select("*")
                        ->where("name", $_ROUTEVAR[1])
                        ->first();


            if (BlogController::myBlogRole($blog["id"]) == "OWNER") {
                return view("blog/appeareance", [
                    "blog"=>$blog,
                    "myRank"=>BlogController::myBlogRole($blog["id"])
                ]);
            }
        }
    }

    public static function save(){
        global $_ROUTEVAR;
        if (User::loggedIn()) {
            $blog = (new BlogsTable)
                        ->select("*")
                        ->where("name", $_ROUTEVAR[1])
                        ->first();


            if (BlogController::myBlogRole($blog["id"]) == "OWNER") {
                $update = new BlogsTable;
                if (isset($_POST["picture"]) && $_POST["picture"] != null && $_POST["picture"] != "null" )
                    $update->picture = PreventXSS::preventXSS($_POST["picture"]);

                if (isset($_POST["description"]))
                    $update->description = $_POST["description"];
                
                if (isset($_POST["homepage"]))
                    $update->homepage = $_POST["homepage"];
                $update->update()->byVars()->where("id", $blog["id"])->run();
               // echo $update->query;
                var_dump($update);
            }
        }
    }



}