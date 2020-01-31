<?php tmpl("header",["title"=>htmlspecialchars($blog["name"])."'s Blog"]); ?>
    <app>
        <div style="display: flex" id="footer_seperator">
            <?php view("nav/blog", ["navBlog"=>$blog, "userRank"=>$myRank]); ?>
            <div class="contents_first">
                
                <h2 style="margin-bottom: 4px; margin-top: 10px;" class="center-title">Members</h2>
                <h3 class="center-title">Manage your Team-Members!</h3>
                <input type="text" class="flatInput" id="userautocomplete">
                <a class="btn blue" id="add_user">Add</a>
                <div id="userautocompletion"></div><script src="/assets/js/userautocomplete.js"></script>
                <br>
                <?php foreach($members as $member):?>
                    <div class="members_member">
                        <a class="members_member_name"><?php echo ($member->username); ?></a>
                        <div style="margin-left: auto;">
                            <?php if($member->rank != "OWNER"):?>
                                <select user="<?php echo ($member->id); ?>" class="user_rank">
                                    <option value="ADMIN" <?php if($member->rank=="ADMIN"):?>selected<?php endif; ?>>ADMIN</option>
                                    <option value="WRITER" <?php if($member->rank=="WRITER"):?>selected<?php endif; ?>>WRITER</option>
                                </select>
                                <a class="members_member_delete btn red" user="<?php echo ($member->id); ?>" >Remove</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>        
        </div>
        <style>

            .user_rank {
                background: var(--block-background);
                vertical-align: top;
                margin-top: 7px;
                border: #00000022;
                color: var(--text-color);
                padding: 5px 15px;
                margin-right: 10px;
                border-radius: 4px;
                box-shadow: 0px 2px 2px 0px #00000033;
            }

            .members_member {
                display: flex;
                border-bottom: 1px solid #00000022;
            }
            .members_member_name {
                padding: 10px 0px;
                display: block;
            }

            #userautocomplete {
                width: calc(100% - 102px);
                vertical-align: top;
                background: transparent;
                color: var(--text-color);
                border: none;
                font-size: 27px;
                padding: 0px;
                border-bottom: 2px #00000022 solid;
            }
        </style>
        <script>
            $(".members_member_delete").click(function(){
                showSnackBar("Deleting...", "#d66f1a");
                Cajax.post("/<?php echo ($blog["name"]); ?>/a/members/remove", {
                    userid: $(this).attr("user")
                }).then(function(res) {
                    const parsed = JSON.parse(res.responseText);
                    if (parsed.done){
                        window.location = "";
                        showSnackBar("Done!");
                    }
                }).send();
            });

            $(".user_rank").on("change", function(){
                showSnackBar("Changing role...", "#d66f1a");
                Cajax.post("/<?php echo ($blog["name"]); ?>/a/members/changerank", {
                    userid: $(this).attr("user"),
                    rank: $(this).val()
                }).then(function(res) {
                    const parsed = JSON.parse(res.responseText);
                    if (parsed.done){
                        showSnackBar("Done!");
                    }
                }).send();
            });

            $("#add_user").click(function(){
                showSnackBar("Adding member...", "#d66f1a");
                Cajax.post("/<?php echo ($blog["name"]); ?>/a/members/add", {
                    username: $("#userautocomplete").val()
                }).then(function(res) {
                    if (res.responseText == "true")
                        window.location = "";
                    else
                        showSnackBar("Error", "#3232EE");
                }).send();
            });
        </script>
    </app>
<?php tmpl("footer"); ?>