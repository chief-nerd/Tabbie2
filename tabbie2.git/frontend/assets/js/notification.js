$( document ).ready(function() {

    var socket = io.connect('http://localhost:8890');

    socket.on('notification', function (data) {

        var message = JSON.parse(data);

        message.debate = '6163';

        if(message.message === 0){
        	getElementByAttribute('data-key', message.debate).cells[3].innerHTML = 'Molly McParland';
        	//cell.innerHTML = '<span class="glyphicon glyphicon-ok text-danger"></span>';
        	alert(getElementByAttribute('data-key', message.debate).cells[3].innerHTML);
        	
        }
        if(message.message === 1){
        	//getElementByAttribute('data-key', message.debate).cells[9].innerHTML = '<span class="glyphicon glyphicon-remove text-success"></span>';
        	//var judge = debate.item(3).innerHTML;
        	alert("Go to the bathroom.")
        }

        //$( "#notifications" ).prepend( "<p><strong>" + message.name + "</strong>: " + message.message + "</p>" );

    });

});

function getElementByAttribute(attr, value, root) {
    root = root || document.body;
    if(root.hasAttribute(attr) && root.getAttribute(attr) == value) {
        return root;
    }
    var children = root.children, 
        element;
    for(var i = children.length; i--; ) {
        element = getElementByAttribute(attr, value, children[i]);
        if(element) {
            return element;
        }
    }
    return null;
}