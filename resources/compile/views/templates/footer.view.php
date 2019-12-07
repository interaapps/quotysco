    <div id="footer">
        <div class="col_1000px">
            <img src="/assets/images/big_logo_white.png" width="200px" style="user-select: none">
            @if((!\app\classes\User::loggedIn()))#
                <br><br>
                <a href="https:/accounts.interaapps.de/iaauth/9" class="btn">Start</a>
            @endif
        </div>
        <br>
        <div class="col_1000px col_container">
            <div class="col_500px p10px">
                <h2>Information</h2><br>
                <a href="https://interaapps.de/imprint">Imprint</a><br>
                <a href="https://interaapps.de/dsgvo">DSGVO</a><br>
            </div>
            <div class="col_500px p10px">
                <h2>quotysco</h2><br>
                <a href="https://interaapps.de">InteraApps</a><br>
                <a href="https://github.com/interaapps/quotysco">GitHub</a><br>
            </div>
        </div>
    </div>
    <script>
    try {
        Waves.attach(".btn", ["waves-light"]);
        Waves.attach(".ripple");
        Waves.init();
    } catch (error) {}
    </script>
    @if(( isset($_COOKIE["darktheme"]) && $_COOKIE["darktheme"] == "auto" ))#
    <style>
    @media (prefers-color-scheme: light) {
        :root {
            --background: #FFFFFF;
            --block-background: #FFFFFF;
            --text-color: #323232;
            --link-color: #434343;
        }
    }

    @media (prefers-color-scheme: dark) {
        :root {
            --background: #1a1e27;
            --block-background: #212531;
            --text-color: #FFFFFF;
            --link-color: #FFFFFF;
        }
    }
    </style>
    @endif
</body>
</html>