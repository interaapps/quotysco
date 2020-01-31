var currentFocus = 0;
var autocompletionList = [];

function reloadUserautocompletion(reloadList) {
    Cajax.post("/user/search", {
        search: $("#userautocomplete").val()
    }).then(function(resp) { //profileimage
        parsedJSON = JSON.parse(resp.responseText);
        autocompletionList = parsedJSON.user;
        if (autocompletionList.length == 1) {
        if (autocompletionList[0].username == $("#userautocomplete").val()) {
            autocompletionList = [];
        }
    }
    if (reloadList)
        setUserautocompletion(autocompletionList);
    }).send();
}

function setUserautocompletion(arr) {
    $("#userautocompletion").html("");
    for (var i=0; arr.length > i; i++) {
        $("#userautocompletion").html($("#userautocompletion").html()+ "<div entryid='"+i+"'"+( currentFocus==i ? " class='userautocompletionfocussed'" : "" )+"><img src='"+arr[i].profileimage+"' /><span>"+arr[i].username+"</span></div>");
    }
    $("#userautocompletion div").click(function() {
        currentFocus = $(this).attr("entryid");
        $("#userautocomplete").val($("[entryid='"+currentFocus+"'] span").text());
        autocompletionList = [];
        setUserautocompletion(autocompletionList);
    });
}

$("#userautocomplete").on("focusout", function() {
    setTimeout(() => {
        autocompletionList = [];
        setUserautocompletion(autocompletionList);
    }, 500);
});


$("#userautocomplete").on("focus",function() {
    reloadUserautocompletion(true);
});

$("#userautocomplete").on("keyup", function(event) {
    var reloadList = true;
    console.log(event.keyCode);
    if (event.keyCode == 40) {
        currentFocus++;
        if (currentFocus >= autocompletionList.length)
            currentFocus = 0;
        setUserautocompletion(autocompletionList);
        return false;
    } else if (event.keyCode == 27) {
        autocompletionList = [];
        setUserautocompletion(autocompletionList);
        event.preventDefault();
        reloadList = false;
        return;
    } else if (event.keyCode == 38) {
        currentFocus--;
        if (currentFocus == -1)
            currentFocus = autocompletionList.length-1;
        setUserautocompletion(autocompletionList);
        return false;
    } else if (event.keyCode == 13 || event.keyCode == 9) {
        $("#userautocomplete").val($("[entryid='"+currentFocus+"'] span").html());
        autocompletionList = [];
        setUserautocompletion(autocompletionList);
        reloadList = false;
        event.preventDefault();
    } else if (event.keyCode == 39 || event.keyCode == 37) {
        event.preventDefault();
        return;
    } else {
        currentFocus = 0;
    }
    
    reloadUserautocompletion(reloadList);
    
    
});

$('#userautocomplete').on('keydown', function(e) {
    var keyCode = e.keyCode || e.which; 
    if (keyCode == 9) 
        e.preventDefault(); 
});