$(document).ready(function() {
    if (!Modernizr.video.webm) {
        $("#no-webm-support").show();
    }
});