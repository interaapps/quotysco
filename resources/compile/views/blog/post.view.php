@template(("header",[
    "title"=>htmlspecialchars($blog["name"])."'s Blog",
    "extrameta"=>'
    <meta property="og:type" content="article" />
    <meta name="og:title" property="og:title" content="'.htmlspecialchars($postTitle).'">
    <meta name="twitter:creator" property="twitter:creator" content="'.htmlspecialchars($blog["name"]).'">
    <meta name="twitter:site" property="twitter:site" content="'.str_replace("\n"," ",substr(strip_tags($contents), 0, 100)).'...">
    <meta property="og:url" content="'.((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]").'" />
    <meta property="og:title" content="'.htmlspecialchars($postTitle).'" />
    <meta property="og:description" content="'.str_replace("\n"," ",substr(strip_tags($contents), 0, 50)).'..." />
    <meta name="Description" content="'.str_replace("\n"," ",substr(strip_tags($contents), 0, 150)).'">
    '.
    ($image != null ?
    '
    <meta name="twitter:image" content="'.htmlspecialchars($image).'">
    <meta property="og:image" content="'.htmlspecialchars($image).'" />
    '
    : "" )
]))!

<script>hljs.initHighlightingOnLoad();</script>
    <app>
        <div style="display: flex" id="footer_seperator">
            @view(("nav/blog", ["navBlog"=>$blog, "userRank"=>$myRank]))!
            <div class="contents_first">
                <h1 id="post_title">{{ $postTitle }}</h1>
                <p id="post_info">{{$information}}</p>
                <a href="/{{$blog["name"]}}" id="post_user">
                    <img id="post_user_profilepic" src="{{$blog["picture"]}}" />
                    <div>
                        <span id="post_user_name">{{$blog["name"]}}</span>
                        <p id="post_user_description">{{$blog["description"]}}</p>
                    </div>
                </a>
                @if(($image!=null))#
                <img id="post_image" src="{{$image}}" />
                @endif
                <div id="post_overview">
                    <h3>Overview:</h3>
                </div>
                <div id="post_contents">
                    {{ $contents }}
                </div>
                <div>
                @if(($myRank !== false))#
                    <br><br><br>
                    <a href="/{{$blog["name"]}}/{{$post["link"]}}/edit" class="btn blue">Edit</a>
                    <a href="/{{$blog["name"]}}/delete/{{$post["id"]}}" class="btn red">Delete</a>
                @endif
                </div>

                <a id="comments_button" class="btn qred" style="display:block">Comments</a>
                <div id="new_comment_container">
                    <textarea id="new_comment_input" placeholder="Enter a comment"></textarea>
                    <a id="new_comment_send" class="btn green" style="display:block">Send</a>
                </div>
                <div id="comments">
                        
                </div>
            </div>
        </div>
    </app>

    <style>
    #comments_button {
        display: block;
        border: #c80048 solid 2px;
        background: transparent;
        color: #c80048;
        padding: 14px;
        margin-top: 20px;
    }

    #new_comment_input {
        width: 100%;
        border: #c80048 solid 2px;
        padding: 10px;
        box-sizing: border-box;
        margin: 15px 0px;
        border-radius: 5px;
        background: transparent;
        color: var(--text-color);
    }

    #post_overview {
        position: relative;
        left: 800px;
        top: 60px;
    }

    #post_overview a:link,
    #post_overview a:active,
    #post_overview a:visited {
        color: #c80048;
        text-decoration: none;
        display: block;
    }

    #post_overview h3 {
        color: #c80048;
        margin-bottom: 10px;
    }
    </style>

    <script>

    $("#post_contents h1, #post_contents h2").each(function(element){
        console.log(element);
        var id = "contents_"+element.tagName+"_"+element.innerText.replace(/[\W_]+/g,"_");
        element.id = id;
        if (element.tagName == "H1")
            $("#post_overview").append($n("a").attr("href", "#"+id).text(element.innerText));
        else if (element.tagName == "H2")
            $("#post_overview").append($n("a").style({
                "paddingLeft": "10px"
            }).attr("href", "#"+id).text(element.innerText));
            
    });

    function escapeHtml(text) {
       return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }

    @if((!\app\classes\User::loggedin()))#
        $("#new_comment_input").click(function(e){
            open("https://accounts.interaapps.de/iaauth/9");
            e.preventDefault();
        });
    @endif

    function loadComments(page=0){
        showSnackBar("Loading...", "#d66f1a");
        Cajax.get("/{{$blog["name"]}}/{{$post["link"]}}/comments", {
            page: page
        }).then(function(res){
                const parsed = JSON.parse(res.responseText);
                for (obj in parsed) {
                    obj = parsed[obj];
                    var deleteComment = "";

                    @if((\app\classes\User::loggedin()))#
                    if ( obj.user.id == {{\app\classes\User::getUserObject()->id}} || {{$myRank !== false ? "true" : "false"}} )
                        deleteComment = '<i class="delete_comment material-icons" commentid="'+obj.comment.id+'" style="cursor:pointer; vertical-align: middle; font-size: 19px; margin-left: 5px;">delete</i>';
                    @endif

                    $("#comments").append(`<div class="blog_post">
                            <span class="blog_post_date">`+obj.comment.created+` `+deleteComment+`</span>
                            
                            <div class="blog_post_user">
                                <img class="blog_post_user_image" src="`+obj.user.pb+`" />
                                <span class="blog_post_user_name">`+obj.user.name+`</span>
                            </div>
                                <div class="blog_post_contents">
                                    `+escapeHtml(obj.comment.contents)+`
                                </div>
                        </div>`);

                        $(".delete_comment").click(function(){
                            showSnackBar("Deleting...", "#d66f1a");
                            Cajax.post("/{{$blog["name"]}}/{{$post["link"]}}/comments/remove", {
                                id: $(this).attr("commentid")
                            }).then(function(res){
                                $("#comments").html("");
                                loadComments();
                                showSnackBar("Deleted!");
                            }).send();
                        });

                        showSnackBar("Loading... - Comment: "+obj.comment.id, "#d66f1a");
                }

                showSnackBar("Done...");

            }).send();
    }

    var firstLoad = false;

    $(document).ready(function(){
        $("#comments").hide();
        $("#new_comment_send").hide();
        $("#new_comment_container").hide();
        $("#comments_button").click(function(){
            $("#comments").toggle();
            $("#new_comment_container").toggle();
            if (!firstLoad) {
                loadComments();
                firstLoad = true;
            }
        });

        $("#new_comment_input").on("focus", function(){
            $("#new_comment_send").show();
        });

        $("#new_comment_input").on("focusout", function(){
            if ($("#new_comment_input").val() == "")
                $("#new_comment_send").hide();
        });

        $("#new_comment_send").click(function(){
            showSnackBar("Saving...", "#d66f1a");
            Cajax.post("/{{$blog["name"]}}/{{$post["link"]}}/comments/add", {
                contents: $("#new_comment_input").val()
            }).then(function(res){
                showSnackBar(res.responseText);
                if (res.responseText == "Done") {
                    $("#comments").html("");
                    loadComments();
                }
                $("#new_comment_input").val("");
                $("#new_comment_send").hide();
                
            }).send();
        });
        
    });
    </script>
    
    <!--<div id="rofl">TEST</div> FINSISH: Selection tools

    <script>
    $("#post_contents").on("mouseup", function(){
        if (document.getSelection().toString() !== "") {
            console.log(document.getSelection());
            s = window.getSelection();
            oRange = s.getRangeAt(0);
            oRect = oRange.getBoundingClientRect();
            console.log(document.documentElement.scrollTop+oRect.y);
            $("#rofl").css({
                position: "relative",
                top: (document.documentElement.scrollTop)+"px",
                left: oRect.x+"px"
            });
        }
    });
    </script>-->

@template(("footer"))!