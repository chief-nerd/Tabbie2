/**
 * Flash functions
 */
function addFlash(type, msg) {
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

$(document).on("click", "div.flashes .alert", function () {
    $(this).remove();
});

/**
 * Loading Animation fadein and start
 */
$(document).on("click", ".loading", function () {
    $("#loader").fadeIn();
});

/**
 * Defer loading
 */
$('img[data-src]').each(function (index, elem) {
    elem.src = elem.dataset.src;
});