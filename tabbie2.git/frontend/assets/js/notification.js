$(document).ready(function () {

    var socket = io.connect('http://192.168.1.80:8890');

    socket.on('notification', function (data) {

        var message = JSON.parse(data);
        /*
         To do with results confirmation.
         */
        if (message.type === 'confirmation') {
            if (message.message === 0) {
                //getElementByAttribute('data-key', message.debate).cells[3].innerHTML = 'Molly McParland';
                getElementByAttribute('data-key', message.debate).cells[9].innerHTML = '<span class="glyphicon glyphicon-remove text-danger"></span>';
                console.log('Debate ' + message.debate + ' unconfirmed.')
                //alert(getElementByAttribute('data-key', message.debate).cells[3].innerHTML);

            }
            if (message.message === 1) {
                //getElementByAttribute('data-key', message.debate).cells[3].innerHTML = 'Molly McParland';
                getElementByAttribute('data-key', message.debate).cells[9].innerHTML = '<span class="glyphicon glyphicon-ok text-success"></span>';
                console.log('Debate ' + message.debate + ' confirmed.')
                //var judge = debate.item(3).innerHTML;
                //alert(message.debate);
            }
        }

        if (message.type === 'result') {
            console.log(message);
            getElementByAttribute('data-key', message.debate).cells[4].innerHTML = message.og_place;
            getElementByAttribute('data-key', message.debate).cells[5].innerHTML = message.oo_place;
            getElementByAttribute('data-key', message.debate).cells[6].innerHTML = message.cg_place;
            getElementByAttribute('data-key', message.debate).cells[7].innerHTML = message.co_place;
            getElementByAttribute('data-key', message.debate).cells[9].innerHTML = '<span class="glyphicon glyphicon-remove text-danger"></span>';
        }

        if (message.type === 'changejudge') {
            console.log($('.btn.adj.label[data-id="' + message.id + '"]').parent().index());
            console.log(message.pos);
            console.log(message.new_panel);

            if ($('.btn.adj.label[data-id="' + message.id + '"]').length) {

                if ($('.adj_panel[data-panel="' + message.new_panel + '"]').length) {
                    if (message.pos === 0) {
                        //put it first
                        console.log('Putting it first');
                        $('.btn.adj.label[data-id="' + message.id + '"]').parent().prependTo($('.adj_panel[data-panel="' + message.new_panel + '"]'));
                    }
                    else {
                        //put it after the one before it
                        console.log('Not putting it first');
                        $('.btn.adj.label[data-id="' + message.id + '"]').parent().appendTo($('.adj_panel[data-panel="' + message.new_panel + '"]'));
                    }
                }
                else {
                    $('.btn.adj.label[data-id="' + message.id + '"]').parent().remove();
                }
            }

            else {

                var strength = message.strength;
                var name = message.name;
                var judgeid = message.id;
                var tournamentslug = 'oxford-iv-';
                var roundid = '801';

                console.log(strength);
                console.log(name);

                var toInsert = '<li role="option" aria-grabbed="false"><span class="handle"><span class="glyphicon glyphicon-move"></span></span><button type="button" class="btn btn-sm adj label toLoad st' + Math.floor(strength / 10) + '  OTHE" data-id="' + judgeid + '" data-strength="' + strength + '" data-href="/oxford-iv-/adjudicator/popup/' + judgeid + '/round_id/' + roundid + '" data-toggle="popover-x" data-placement="bottom" data-target="#w6">' + name + '</button></li>';

                if (message.pos === 0) {
                    //put it first
                    console.log('Putting it first');
                    $('.adj_panel[data-panel="' + message.new_panel + '"]').prepend(toInsert);
                }
                else {
                    //put it after the one before it
                    console.log('Not putting it first');
                    $('.adj_panel[data-panel="' + message.new_panel + '"]').append(toInsert);
                }
            }
        }

        if (message.type === 'roundUpdate') {
            var obj = message.data;
            if (typeof obj.newPanel != "undefined" && typeof obj.oldPanel != "undefined") {
                console.log("Saved!");

                var obj = [obj.newPanel, obj.oldPanel];
                var noticeString = '<span class="glyphicon glyphicon-info-sign text-gray"></span>';
                var warningString = '<span class="glyphicon glyphicon-warning-sign text-warning"></span>';
                var errorString = '<span class="glyphicon glyphicon-exclamation-sign text-danger"></span>';

                for (index = 0; index < obj.length; ++index) {
                    panel = obj[index];

                    var warning = false;
                    var error = false;
                    var notice = false;

                    var newLine = $("#debateDraw-container tr[data-key=" + panel.id + "] > td:nth-last-child(2)");
                    for (var i = 0; i < panel.messages.length; i++) {
                        if (panel.messages[i].key == "error" && panel.messages[i].penalty > 0)
                            error = true;
                        if (panel.messages[i].key == "warning" && panel.messages[i].penalty > 0)
                            warning = true;
                        if (panel.messages[i].key == "notice" && panel.messages[i].penalty > 0)
                            notice = true;
                    }
                    var html = "";
                    if (notice)
                        html += noticeString + "&nbsp;";
                    if (warning)
                        html += warningString + "&nbsp;";
                    if (error)
                        html += errorString + "&nbsp;";

                    newLine.html(html);
                }

                th = $("#debateDraw-container thead th:last-child i");
                th[0].className = "glyphicon glyphicon-ok-circle text-success";
            }
        }
    });

});

function getElementByAttribute(attr, value, root) {
    root = root || document.body;
    if (root.hasAttribute(attr) && root.getAttribute(attr) == value) {
        return root;
    }
    var children = root.children,
        element;
    for (var i = children.length; i--;) {
        element = getElementByAttribute(attr, value, children[i]);
        if (element) {
            return element;
        }
    }
    return null;
}