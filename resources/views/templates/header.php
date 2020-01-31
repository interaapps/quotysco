<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet">
    <link rel="stylesheet" href="https://indestructibletype.com/fonts/Jost.css" type="text/css" charset="utf-8" />

    <!-- Style -->
    <link rel="stylesheet" href="/assets/css/app.css">

    <!-- Scripts -->
    <script src="/assets/js/app.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.13.1/highlight.min.js"></script>

    <?php if(isset($extrameta)):?>
    <?php echo ($extrameta); ?>
    <?php endif; ?>

    <title><?php echo ($title); ?></title>
</head>
<body>
    <div id="nav">
        <a id="logo" href="/"><img src="/assets/images/big_logo.png" /></a>
        <?php if( \app\classes\User::loggedIn() ):?>
        <a id="userprofile"><img src="<?php echo (\app\classes\User::getUserObject()->profilepic); ?>" /></a>
        <div id="navdropdown">
            <a href="/" class="navdropdownlink"><i class="material-icons-outlined">home</i><span>Home</span></a>    
            <a href="/account" class="navdropdownlink"><i class="material-icons-outlined">person</i><span>My Account</span></a>

            <a class="navdropdownbadge">MY BLOGS</a>
            <?php foreach( (new \databases\BlogUsersTable)->select('blogid')->where("userid", \app\classes\User::getUserObject()->id)->get() as $blogUser ):?>
                <?php
                $blog = (new \databases\BlogsTable)->select('*')->where("id", $blogUser["blogid"])->first(); ?>
                <a href="/<?php echo htmlspecialchars($blog["name"]); ?>" class="navdropdownlink"><img src="<?php echo htmlspecialchars($blog["picture"]); ?>" /><span><?php echo htmlspecialchars($blog["name"]); ?></span></a>
            <?php endforeach; ?>
            <a href="/newblog" class="navdropdownlink"><i class="material-icons-outlined">add</i><span>New Blog</span></a>

            <a style="cursor:pointer;" id="darkthemeswitch" class="navdropdownlink"><i class="material-icons-outlined">nights_stay</i><span>Darktheme</span></a>
        </div>
        
        <style>
        <?php if( strlen(app\classes\User::getUserObject()->color) == 7 ):?>
        #userprofile img {
            border: solid 2px <?php echo (app\classes\User::getUserObject()->color); ?>;
        }
        <?php endif; ?>
        </style>
        <?php else: ?>
            <a href="https://accounts.interaapps.de/iaauth/9" class="btn qred" id="userprofile" style="float: right; line-height: 20px;">Login</a>
            
            <style>
                #userprofile {
                    padding-bottom: 10px !important;
                    padding-left: 22px !important;
                }
                
            </style>
        <?php endif; ?>
    </div>