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
            if (!($(this).hasClass("alert-error"))) {
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

$(document).on("click", "div.flashes .alert.success", function () {
    $(this).remove();
});

/**
 * Loading Animation fadein and start
 */
$(document).on("click", ".loading", function () {
    var loader = $("#loader");
    var img = document.createElement("img");
    var cont = document.createElement("div");

    img.src = loader[0].dataset.url;
    img.alt = "Loader";

    cont.className = "container";
    cont.appendChild(img);
    loader[0].appendChild(cont);
    loader.fadeIn();
});