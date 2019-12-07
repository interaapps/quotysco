<?php if (file_exists("." . $_SERVER['REQUEST_URI'])) return false; ?><?php
/*if (preg_match("/\.(?:png|jpg|jpeg|gif|js|css|html)$/", $_SERVER["REQUEST_URI"])) {return false;}*/
/*file_put_contents("../.temp.temp",$_SERVER["REQUEST_URI"]);*/
?><?php
chdir('..');

// IMPORTING STANDARD LIBS
require "ulole/loader.php";

loadCore();

// Initializing routings
$router = new Router();
require "app/route.php";
$router->setDirectories($views_dir, $templates_dir);

$router->route();