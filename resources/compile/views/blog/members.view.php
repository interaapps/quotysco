@template(("header",["title"=>htmlspecialchars($blog["name"])."'s Blog"]))!
    <app>
        <div style="display: flex">
            @view(("nav/blog", ["navBlog"=>$blog, "userRank"=>$myRank]))!
            <div class="contents_first">
                <input type="text" class="flatInput" id="userautocomplete">
                <a class="btn blue">Add</a>
                <div id="userautocompletion"></div><script src="/assets/js/userautocomplete.js"></script>
                <br>
                @foreach(($members as $member))#
                    <div class="members_member">
                        <a class="members_member_name">{{$member->username}}</a>
                        <div style="margin-left: auto;">
                            @if(($member->rank != "OWNER"))#
                                <a class="members_member_delete" user="{{$member->id}}" class="btn red">Remove</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>        
        </div>
        <style>
            .members_member {
                display: flex;
                border-bottom: 1px solid #00000022;
            }
            .members_member_name {
                padding: 10px 0px;
                display: block;
            }
        </style>
        <script>
            $(".members_member_delete").click(function(){

            });
        </script>
    </app>
@template(("footer"))!