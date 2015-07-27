window.DoScroll = false;
window.ScrollSpeed = 7;
window.current_top = 0;
window.ScrollUntil = $("#team-table-container")[0].offsetHeight;
$('#team-table-container .table').css("position", "relative");

notEnd = true;

function pageScroll() {
    window.scrollBy(0, 1);
    //$('#team-table-container .table').css("top", window.current_top);

    if (window.scrollY < (window.ScrollUntil + 40)) {
        notEnd = true;
    }
    else {
        notEnd = false;
        window.DoScroll = false;
        window.scrollTo(0, 0);
        $('#team-table').hide(); //Hide Table to reduce clutter
        $('#drawdisplay').css("display", "block");
        if ($("#infoslide").length > 0)
            $("#infoslide").removeAttr("disabled");
    }

    if (window.DoScroll == true && notEnd)
        scrolldelay = setTimeout('pageScroll()', (10 - window.ScrollSpeed) * 10); // scrolls every 100 milliseconds
}

$("a.run").on("click", function (e) {
    e.preventDefault();
    startScroll();
});

function startScroll(init) {
    if (!window.DoScroll) {
        $("a.run").html("Pause");
        if ($("li > a.reduce").length == 0) {
            Minus = "<li><a class='reduce btn'><i class='glyphicon glyphicon-minus-sign'></i> Reduce</a></li>";
            Plus = "<li><a class='add btn'><i class='glyphicon glyphicon-plus-sign'></i> Add</a></li>";
            Speed = "<li class='speed'><a>Speed: <span>" + window.ScrollSpeed + "</span></a></li>";
            $("ul.navbar-right").append(Minus);
            $("ul.navbar-right").append(Plus);
            $("ul.navbar-right").append(Speed);
        }
        window.DoScroll = true;
        var h = window.innerHeight
            || document.documentElement.clientHeight
            || document.body.clientHeight;
        $("#team-table").css("margin-bottom", h - 100);
        if (!init) init = 2000;

        setTimeout('pageScroll()', init);
    }
    else {
        $("a.run").html("Run");
        window.DoScroll = false;
    }
}

$(document).on("ready", function () {
    startScroll(5000);
});

$(document).on("click", "a.add", function (e) {
    e.preventDefault();
    window.ScrollSpeed++;
    $("li.speed span").html(window.ScrollSpeed);
    console.log("Add to " + window.ScrollSpeed);
});


$(document).on("click", "a.reduce", function (e) {
    e.preventDefault();
    if (window.ScrollSpeed > 1)
        window.ScrollSpeed--;
    $("li.speed span").html(window.ScrollSpeed);
    console.log("Reduce to " + window.ScrollSpeed);
});

$(document).on("click", "#infoslide", function (e) {
    e.preventDefault();
    $("#infoslideContent").fadeIn();
    $("#motion").removeAttr("disabled");
});

function confirm() {
    href = $("#motionContent").data("href");
    $.ajax({
        type: "GET",
        url: href
    }).success(function (data) {
        if (data == "1")
            console.log("Set");
        else
            console.log("Ups");
    }).error(function (jqXHR, textStatus, errorThrown) {
        console.error(textStatus + " : " + errorThrown);
    });
}

$(document).on("click", "#motion", function (e) {
    e.preventDefault();
    if ($("#infoslideContent").length > 0) {
        $("#infoslideContent").fadeOut(500, function () {
            $("#motionContent").fadeIn(500, confirm);
        });
    }
    else
        $("#motionContent").fadeIn(500, confirm);
});