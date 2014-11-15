//adj
$(".adj").on("click.target.popoverX", function () {
    href = $(this).data("href");
    root = document.querySelector($(this).data("target"));
    contentdiv = $(root).find(".popover-content");
    if (contentdiv[0].innerHTML.length <= 13)
    {
        $.ajax({
            type: "GET",
            url: href,
        }).success(function (data) {
            $(contentdiv).html(data);
            root.style.top = (parseInt(root.style.top) - 60) + "px";
        }).error(function (jqXHR, textStatus, errorThrown) {
            console.error(textStatus + " : " + errorThrown);
        });
    }
});