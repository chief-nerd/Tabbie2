href = $(".rounds").data("href");

(function poll() {
    setTimeout(function () {
        $.ajax({
            type: "GET",
            url: href,
        }).success(function (data) {
            if (data.length > 0)
            {
                $(".rounds").html(data);
            }
            else
                console.log("Nothing yet");
        }).error(function (jqXHR, textStatus, errorThrown) {
            console.error(textStatus + " : " + errorThrown);
        }).complete(poll);
    }, 3000);
})();