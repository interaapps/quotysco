@template(("header",["title"=>"404"]))!
<?#
$blogs = [];
?>
<app>
    <div class="contents_first">


    <h1 class="center-title">Newest Posts</h1>

    @foreach(((new databases\PostsTable)->select("*")->order(" id DESC")->limit(7)->get() as $post))#
        <?#
        if (!isset($blogs[$post["blogid"]]) ) {
            $blogs[$post["blogid"]] = (new \databases\BlogsTable)->select("*")->where("id", $post["blogid"])->first();
        }
        ?>
        @view(("components/post", [
            "post"=>$post,
            "blog"=>$blogs[$post["blogid"]]
        ]))!
    @endforeach
    </div>
</app>
@template(("footer"))!