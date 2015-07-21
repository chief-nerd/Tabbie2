function init() {
    th = $("#debateDraw-container thead th:last-child");
    th.html(th.html() + " <i></i>");

    /**
     * Function when clicked on a Name in the round/view
     */
    $(".adj").on("click.target.popoverX", function () {
        href = $(this).data("href");
        root = document.querySelector($(this).data("target"));
        contentdiv = $(root).find(".popover-content");
        if (this.classList.contains('toLoad')) {
            this.classList.remove('toLoad');
            $.ajax({
                type: "GET",
                url: href
            }).success(function (data) {
                $(contentdiv).html(data);
                root.style.top = (parseInt(root.style.top)) + "px";
            }).error(function (jqXHR, textStatus, errorThrown) {
                console.error(textStatus + " : " + errorThrown);
            });
        }
    });

    /**
     * Drag and Drop Function
     */
    $(".adj_panel li").on("dragstart", function (e) {
        if ($(this.parentNode).find('li').length > 1) {
            window.dragid = $(this.children[1]).data("id");
            window.panelid = $(this.parentNode).data("panel");
        }
        else {
            addFlash('info', 'There has to be at least 1 adjudicatur per room');
            e.preventDefault();
        }
    });

    $(".adj_panel").on("drop", function (event) {
        //console.log(event);
        href = $("#debateDraw").data("href");
        panel = $(this).data("panel");
        placeholder = $(this).find("li.sortable-placeholder");
        position = $(this).find("li").index(placeholder);
        console.log("#", window.dragid, "from Panel", window.panelid, "to", panel, "at pos", position);

        th = $("#debateDraw-container thead th:last-child i");
        var reloadClass = 'glyphicon glyphicon-refresh';
        th[0].className = reloadClass;

        var reloadSpan = '<span class="' + reloadClass + '"></span>';
        $(this).parent().parent().find("td:nth-last-child(2)").html(reloadSpan);
        $("#debateDraw-container ul[data-panel=" + window.panelid + "]").parent().parent().find("td:nth-last-child(2)").html(reloadSpan);

        $.ajax({
            type: "POST",
            url: href,
            data: {
                id: window.dragid,
                old_panel: window.panelid,
                new_panel: panel,
                pos: position
            }
        }).success(function (data) {
            obj = JSON.parse(data);
            if (typeof obj.newPanel != "undefined" && typeof obj.oldPanel != "undefined") {
                console.log("Saved!");

                var obj = [obj.newPanel, obj.oldPanel];
                var warningString = '<span class="glyphicon glyphicon-warning-sign text-warning"></span>';
                var errorString = '<span class="glyphicon glyphicon-exclamation-sign text-danger"></span>';

                for (index = 0; index < obj.length; ++index) {
                    panel = obj[index];

                    var warning = false;
                    var error = false;

                    var newLine = $("#debateDraw-container tr[data-key=" + panel.id + "] > td:nth-last-child(2)");
                    for (var i = 0; i < panel.messages.length; i++) {
                        if (panel.messages[i].key == "error")
                            error = true;
                        if (panel.messages[i].key == "warning")
                            warning = true;
                    }
                    var html = "";
                    if (warning)
                        html += warningString + "&nbsp;";
                    if (error)
                        html += errorString + "&nbsp;";
                    newLine.html(html);
                }

                th = $("#debateDraw-container thead th:last-child i");
                th[0].className = "glyphicon glyphicon-ok-circle text-success";
            }
            else {
                th = $("#debateDraw-container thead th:last-child i");
                th[0].className = "glyphicon glyphicon-warning-sign text-warning";
                console.log("Return ERROR data", data);
            }
        }).error(function (jqXHR, textStatus, errorThrown) {
            th = $("#debateDraw-container thead th:last-child i");
            th[0].className = "glyphicon glyphicon-remove-circle text-danger";
            console.error(textStatus + " : " + errorThrown);
            console.error(jqXHR.responseText);
        });
    });
}
$("#debateDraw-pjax").on("pjax:end", function () {
    init();
});

$(document).ready(function () {
    init();
});