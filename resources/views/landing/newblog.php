<?php tmpl("header",["title"=>"Welcome"]); ?>
    <app>
        <div class="contents_first">
            <div class="col_1000px col_container">
                <div class="col_1000px">
                    <img id="welcome_done" src="/assets/images/illustrations/done.svg">
                    <h1 style="text-align: center; color: #c80048">Next step, getting started!</h1>
                    <input id="blog_name" type="text" placeholder="My Blog">
                    <p id="error"></p>
                    <a id="create" class="btn disabled">Create</a>
                </div>
            </div>

        </div>
    </app>
    <style>
        #blog_name {
            display: block;
            font-size: 40px;
            width: 100%;
            border: none;
            outline: none !important;
            border-bottom: solid 2px #00000022;
            padding-bottom: 8px;
            transition: border-bottom 0.3s;
            margin-top: 30px;
            background: transparent;
            color: rgb(200, 0, 72);
        }

        #blog_name:focus {
            border-bottom: solid 2px #C80048;
        }

        #create {
            margin-top: 50px;
            display: block;
            font-size: 17px;
        }

        #welcome_done {
            width: 300px;
            max-width: 80%;
            display: block;
            margin: 0px auto;
            margin-bottom: 50px;
        }

        #error {
            display: none;
            font-size: 20px;
            text-align: center;
            padding: 13px;
            background: #fc0341;
            margin-top: 20px;
            color: #FFFFFF;
            border-radius: 5px;
        }
    </style>
    <script>
    $("#blog_name").on("keyup", function(){
        
        Cajax.post("/check/blogname", {
            name: $("#blog_name").val()
        }).then(function(res) {
            const parsed = JSON.parse(res.responseText);
            if (parsed.done) {
                $("#create").addClass("qred");
                $("#create").removeClass("disabled");
                $("#error").text("").css("display","none");
            } else {
                $("#create").addClass("disabled");
                $("#create").removeClass("qred");
                $("#error").text(parsed.error).css("display","block");;
            }
        }).send();

    });

    $("#create").click(function(){
        $("#create").addClass("disabled");
        $("#create").removeClass("qred");
        $("#create").text("Creating...");
        Cajax.post("/newblog", {
            name: $("#blog_name").val()
        }).then(function(res) {
            const parsed = JSON.parse(res.responseText);
            if (parsed.done) {
                window.location = parsed.redirect;
                $("#error").text("").css("display","none");
            } else {
                $("#error").text(parsed.error).css("display","block");;
            }
            $("#create").addClass("qred");
            $("#create").removeClass("disabled");
            $("#create").text("Create");
        }).send();
    });
    </script>
<?php tmpl("footer"); ?>