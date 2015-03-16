// on your javascript
// I highly recommend that you setup the id of your PJax widget

$("input[name='autoupdate']").on('change', function () {
    if ($("input[name='autoupdate']").prop('checked')) {
        window.StopPoll = false;
        poll();
    }
    else {
        $('#pjax-status')[0].className = "";
        window.StopPoll = true;
    }
});

function poll() {
    if (!window.StopPoll) {
        $.pjax.reload({container: '#debates-pjax'});
        setTimeout(function () {
            poll();
        }, 5000);
    }

}
$(document).on('pjax:send', function () {
    $('#pjax-status')[0].className = "glyphicon glyphicon-refresh";
});
$(document).on('pjax:success', function () {
    $('#pjax-status')[0].className = "glyphicon glyphicon-ok-circle";
});
$(document).on('pjax:error', function () {
    $('#pjaxstatus')[0].className = "glyphicon glyphicon-remove-circle";
});