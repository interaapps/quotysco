function checkScroll() {
    if (window.pageYOffset > 1)         
        $("#nav").css("boxShadow", "rgba(0, 0, 0, 0.55) 0px -45px 18px 34px");
    else 
        $("#nav").css("boxShadow", "0px 7px 17px -10px rgba(0,0,0,0)");
}

function setDarkTheme(){
    document.documentElement.style.setProperty('--background', "#1a1e27");
    document.documentElement.style.setProperty('--block-background', "#212531");
    document.documentElement.style.setProperty('--text-color', "#FFFFFF");
    document.documentElement.style.setProperty('--link-color', "#FFFFFF");
}


function setLightTheme(){
    document.documentElement.style.setProperty('--background', "#FFFFFF");
    document.documentElement.style.setProperty('--block-background', "#FFFFFF");
    document.documentElement.style.setProperty('--text-color', "#323232");
    document.documentElement.style.setProperty('--link-color', "#434343");
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
        c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
        }
    }
    return "";
}

var snackBarTimeout;
  
function showSnackBar(text, color="#17fc2e", background="#1e212b") {
    var snackbar = document.querySelector('#snackbar');
    snackbar.textContent = text;
    snackbar.style.color = color;
    snackbar.style.backgroundColor = background;
    snackbar.classList.add('show');
    clearTimeout(snackBarTimeout);
    snackBarTimeout = setTimeout(() => {
        snackbar.classList.remove('show');
    }, 1500);
}

$(document).ready(function(){
    checkScroll();
    window.onscroll = function() {
        checkScroll();
    };

    $("#userprofile").click(function(){
        var element = document.getElementById("navdropdown");
        if (element == null) return;
        if (element.style.display == null || element.style.display == "")
            element.style.display = "block";
        else
            element.style.display = "";
    });

    if (getCookie("darktheme") == "true") {
        setDarkTheme();
        $("#darkthemeswitch i").html("wb_sunny");
        $("#darkthemeswitch span").text("Light Theme");
    } else if(getCookie("darktheme") == "auto") {
        $("#darkthemeswitch span").text("Auto Theme");
    } {
        $("#darkthemeswitch i").html("nights_stay");
        $("#darkthemeswitch span").text("Dark Theme");
    }

    $("#darkthemeswitch").click(function(){
        if (getCookie("darktheme") == "true") {
            setCookie("darktheme", "false", 1000000);
            setLightTheme()
            $("#darkthemeswitch span").text("Dark Theme");
        } else {
            setCookie("darktheme", "true", 1000000);
            setDarkTheme();
            $("#darkthemeswitch span").text("Light Theme");
        } 
    });

    
});