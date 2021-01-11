$(document).ready(function() {
    var anchor = window.location.hash.replace("#", "");
    $(".collapse").collapse('hide');
    $("#" + anchor+"-contents").collapse('show');
});