/**
 * Function when clicked on a Name in the round/view
 */
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

/**
 * Function when a adjudicator is set to a new room
 * view:124
 * @property {this}
 */
function moveAdjudicator(item)
{
    panel = $(this).data("panel");
}

$(".adj_panel li").on("dragstart", function () {
    console.log("dragstart", this, $(this.children[1]).data("id"));
});

$(".adj_panel li").on("drop", function () {
    console.log("drop", this);
});