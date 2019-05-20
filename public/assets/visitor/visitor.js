initialize();

function socket_io()
{
    var socket = io(nodejs_url);
    var random_id = Math.floor((Math.random() * 999999));
    var data = {};
    data.socket_id = visitor_id;
    data.random_id = random_id;
    data.channel = 'visitor';
    socket.emit('initialize', data);

    //socket reconnect
    socket.on("reconnect", function(message)
    {
        var data = {};
        data.socket_id = visitor_id;
        data.random_id = random_id;
        data.channel = 'visitor';
        socket.emit('initialize', data);
    });

    //socket message
    socket.on('message', function(data)
    {
        var action = data.action;
        console.log('action = ' + action);
        
        if(action == 'chat_group')
        {
            chat_message(data.task);
        }
        if(action == 'chat_message')
        {
            chat_message(data.task);
        }
    });
}

function initialize()
{
    ui_display();
    socket_io();
}

function ui_display()
{
    var html = '';

    html += '<div style="height: 520px; width: 350px; background-color: #FFFFFF;">';
    html += '<h1>visitor ' + visitor_id + '</h1>';
    html += '<div id="div_chat_message"></div>';
    html += '<input id="message" type="text" onkeyup="chatbox_onkeyup(event)">';
    html += '</div>';

    $('#app').html(html);
}

function chatbox_onkeyup(event)
{
    if(event.keyCode == 13)
    {
        chatbox_submit();
    }
}

function chatbox_submit()
{
    var data = {};
    data.visitor_id = visitor_id;
    data.message = $('#message').val();
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/visitor/message';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
    $.ajax(ajax);

    $('#message').val('');
}

function chat_message(task)
{
    var html = '';
    html += '<br>' + task.message;
    $('#div_chat_message').append(html);
}

function logout()
{
    loading_show();

    var data = {};
    data.api_token = api_token;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/admin/logout';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        loading_hide();
        login_display();
	}
    $.ajax(ajax);
}