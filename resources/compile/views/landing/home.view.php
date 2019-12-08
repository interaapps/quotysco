@template(("header",["title"=>"Welcome"]))!
    <app>
        <div class="contents_first">
            <div class="col_1000px col_container">
                <div class="col_600px p10px">
                    <h1>Show them your Quote!</h1>
                    <p>
                        For writers and readers.<br>
                        It's a social network for those <span id="switch-names">story writer</span>.
                        A simple and free way to create your blog.
                    </p>
                    <br><br>
                    <a href="https://accounts.interaapps.de/iaauth/9" class="btn qred">Start</a>
                </div>
                <div class="col_500px p10px">
                    <img width="80%" src="/assets/images/illustrations/reading_list.svg">
                </div>
            </div>

            <div class="col_1000px col_container">
                <div class="col_500px p10px">
                    <img width="80%" src="/assets/images/illustrations/sharing_articles.svg">
                </div>    
                <div class="col_500px p10px">
                    <h1>Simple, easy and fast. The focus is on your words!</h1>
                    <p>
                        Login, create your blog, post! Done!<br>
                        Our user friendly interface helps you with creating your awesome little blog!

                        <br><br><a class="darkmode_btn" style="cursor:pointer;color: #EEEEEE" onclick="setDarkTheme()">There is also a dark side!</a>
                    </p>
                    <br><br>
                </div>
            </div>

            <div class="col_1000px col_container">
                <div class="col_1000px">
                    <h2 style="font-size: 30px ;margin: 0px auto; text-align: center; display: block">What are you waiting for?</h2><br>
                    <a style="    margin: 0px auto; display: table; padding: 10px 90px;" href="https://accounts.interaapps.de/iaauth/9" class="btn qred">Start</a>
                </div>
            </div>
        </div>
    </app>
    <style>
        p {
            font-size: 16px;
        }

        .contents_first {
            max-width: none;
        }
        .col_500px img {
            margin: auto;
            display: block;
        }

        #switch-names {
            background: #c80048EE;
            padding: 4px 10px;
            color: #FFFFFF;
            border-radius: 2px;
        }

    </style>

    <script>
    
    $("#switch-names").text("Story Writers");

    const switchNames = [
        "Story Writers",
        "Programmers",
        "Readers",
        "Creators"
    ]; 
    var switchNamesIndex = 0;

    setInterval(function(){
        $("#switch-names").animate({
            opacity: "1"
        },400);
        $("#switch-names").text(switchNames[switchNamesIndex]);
        switchNamesIndex++;
        if (switchNamesIndex >= switchNames.length)
            switchNamesIndex = 0;
        setTimeout(() => {
            $("#switch-names").animate({
              opacity: "0"
            },400);
        }, 2200);
    }, 3000);
    
    </script>
@template(("footer"))!