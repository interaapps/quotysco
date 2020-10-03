<?php tmpl("header",["title"=>"404"]); ?>
<?php
$blogs = [];
?>
<app>
    <div class="contents_first">


    <h1 class="center-title">Newest Posts</h1>

    <?php foreach((new databases\PostsTable)->select("*")->order(" id DESC")->limit(7)->get() as $post):?>
        <?php
        if (!isset($blogs[$post["blogid"]]) ) {
            $blogs[$post["blogid"]] = (new \databases\BlogsTable)->select("*")->where("id", $post["blogid"])->first();
        }
        ?>
        <?php view("components/post", [
            "post"=>$post,
            "blog"=>$blogs[$post["blogid"]]
        ]); ?>
    <?php endforeach; ?>
    </div>
</app>
<?php tmpl("footer"); ?>