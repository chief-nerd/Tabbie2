function setFlash(type, msg) {
    $("div.flashes").append("<div class='alert alert-" + type + " fade in'>" + msg + "</div>");
    triggerFlash();
}

function triggerFlash() {
    $("div.flashes .alert").each(function (i) {
        $(this).hide();
        $(this).delay(i * 1000).slideDown(800, function () {
            if (!$(this).hasClass("nohide")) {
                $(this).delay(9000).slideUp(400, function () {
                    $(this).remove();
                });
            }
        });
    });
}

$(document).ready(function () {
    triggerFlash();
});