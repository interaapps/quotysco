<?php
// Directory for the views
$views_dir      =  "resources/views/";
$templates_dir  =  "resources/views/templates/";

// Routes

if (\app\classes\User::loggedIn()) {
    $router->get("/", "user/home.php");

    $router->post("/fileupload/img", "!files\FileUploadController@file");
    // $router->post("/fileupload/file", "!files\FileUploadController@file");

} else
    $router->get("/", "landing/home.php");

$router->get("/about", "landing/home.php");

$router->get("/explore", "landing/explore.php");

$router->get("/newblog", "!blog\NewBlogController@page");
$router->post("/newblog", "!blog\NewBlogController@createBlog");

$router->get("/accounts/iaauth:login", "!IaAuthController@login");
$router->post("/user/search", "!IaAuthController@searchUser");

$router->post("/check/blogname", "!blog\NewBlogController@checkName");

$group = function($router) {
    $router->get("/", "!blog\BlogController@page");
    $router->get("",  "!blog\BlogController@page");

    $router->get("/u/following", "!blog\BlogController@followed");
    $router->get("/u/follow", "!blog\BlogController@follow");

    $router->get("/a/new", "!blog\BlogController@newPage");
    $router->post("/a/new", "!blog\BlogController@new");

    $router->get("/a/appeareance", "!blog\AppeareanceController@page");
    $router->post("/a/appeareance", "!blog\AppeareanceController@save");

    $router->get("/a/members", "!blog\MembersController@membersPage");
    $router->post("/a/members/remove", "!blog\MembersController@removeUser");
    $router->get("/a/members/add", "!blog\MembersController@addUser");
    $router->post("/a/members/changerank", "!blog\MembersController@changeUserRank");

    $router->post("/a/check/title", "!blog\BlogController@checkName");

    $router->get("/delete/(.*)", "!blog\PostController@delete");
    $router->get("/(.*)/edit", "!blog\PostController@editPost");
    $router->post("/(.*)/edit", "!blog\PostController@saveEditPost");
    $router->get("/(.*)", "!blog\PostController@page");
};


$router->group("/([a-zA-Z0-9@]*)",   $group);

$router->setPageNotFound("error/404.php");