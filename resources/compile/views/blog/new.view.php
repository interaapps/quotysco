@template(("header",["title"=>htmlspecialchars($blog["name"])."'s Blog"]))!
    <app>
        <div style="display: flex" id="footer_seperator">
            @view(("nav/blog", ["navBlog"=>$blog, "userRank"=>$myRank]))!
            <div class="contents_first">
                <p id="post_info">Today</p>
                <input @if((isset($defaultTitle)))# value="{{htmlspecialchars($defaultTitle)}}" @endif style="border: none !important; background: transparent !important;" type="text" placeholder="Title" id="post_title">
                <div id="post_user">
                    <img id="post_user_profilepic" src="{{$blog["picture"]}}" />
                    <div>
                        <a id="post_user_name">{{$blog["name"]}}</a>
                        <p id="post_user_description">{{$blog["description"]}}</p>
                    </div>
                </div>
                
                <img id="post_image" @if((isset($defaultImage)))# src="{{htmlspecialchars($defaultImage)}}" @else src="" @endif />
                
                <a id="filepickeruploadbutton" class="btn qred">Upload Image</a>
                <a id="removeImageButton" class="btn qred">Remove Image</a>
                

                <div id="post_contents">
                @view(("tools/editor", [
                    "defaultEditorValue"=> (isset($defaultEditorValue) ? $defaultEditorValue : "Hello! Change something in here :)" )
                ]))!
                </div>
                <input style="display: none" id="filepickeruploadinput" type="file">

                <p>Link: <span id="title_preview"></span></p>
               
                <br><br>
                @if((!isset($defaultTitle)))#
                    <a class="btn qred" style="float: right" id="send_button">Send</a>
                @endif
            
            </div>    
        </div>
        <script>
        var file = null;
        var filePicker = null;

        $("#post_title").on("change", function(){
            Cajax.post("/{{$blog["name"]}}/a/check/title", {title: $("#post_title").val()}).then(function(res){
                $("#title_preview").text(window.location.protocol+"//"+window.location.host+"/{{$blog["name"]}}/"+res.responseText);
            }).send();
        });
        
        
        function send() {
            Cajax.post("", {
                contents: $("#lehrgaeditor").html(),
                title: $("#post_title").val(),
                file: file
            }).then(function(res){
                if (res.responseText !== "")
                    window.location = res.responseText;
                else
                    window.location = "/{{$blog["name"]}}";
            }).send();
        }

        $("#send_button").click(function(){
            if (filePicker !== null) {
                upload(filePicker, function(f){
                    send();
                });
            } else
                send();
        });


    $("#filepickeruploadinput").on("change", function(){
        var FR = new FileReader();
        FR.addEventListener("load", function(e) {
            if ((e.target.result).includes("data:image/")) 
                $("#post_image").attr("src", e.target.result);
        }); 
        FR.readAsDataURL( this.files[0] );
        filePicker = this;
        
    });

    function upload(thi, done=function(f){}){
        if (thi.files && thi.files[0]) {   
            var FR = new FileReader();
            var files = thi.files;
            FR.addEventListener("load", function(e) {
                $("#filepickeruploadpreview").attr("src", "");
                if ((e.target.result).includes("data:image/")) 
                    $("#filepickeruploadpreview").attr("src", e.target.result);
                
                request = {
                    file: e.target.result
                };
                
                if (files[0]) {
                    request.fileName = files[0].name;
                }
                Cajax.post("/fileupload/img", request).then(function(resp) {
                    parsedJSONReadFilePicker = JSON.parse(resp.responseText);
                    if (parsedJSONReadFilePicker.done) {
                        file = parsedJSONReadFilePicker.file;
                        done(parsedJSONReadFilePicker.file);
                    } 
                }).send();
            }); 
            FR.readAsDataURL( thi.files[0] );
        }
    }

    $("#filepickeruploadbutton").click(function() {
        $("#filepickeruploadinput").click();
    });

    $("#removeImageButton").click(function(){
        file = "";
        filePicker = null;
        $("#post_image").attr("src", "");
    });


    </script>
        <style>
        [src=""] {
            display: none;
        }

        [src=""] ~ #removeImageButton {
            display: none;
        }
        
        </style>
    </app>
@template(("footer"))!