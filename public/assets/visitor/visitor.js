var visitor = {};
var chat_room = {};
var chat_display = {};

initialize();

function loading_show()
{
    $('#loading_overlay').show();
    $('#loading_content').show();
}

function loading_hide()
{
    $('#loading_overlay').hide();
    $('#loading_content').hide();
}

function cropper_show(html_container, html_footer)
{
    $('#cropper_container').html(html_container);
    $('#cropper_footer').html(html_footer);
    $('#cropper_box').show();
    $('#cropper_footer').show();
}

function cropper_hide()
{
    $('#cropper_box').hide();
    $('#cropper_footer').hide();
}

function socket_io()
{
    var socket = io(nodejs_url);
    var random_id = Math.floor((Math.random() * 999999));
    var data = {};
    data.socket_id = visitor.id;
    data.random_id = random_id;
    data.channel = 'visitor';
    socket.emit('initialize', data);

    //socket reconnect
    socket.on("reconnect", function(message)
    {
        var data = {};
        data.socket_id = visitor.id;
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
    var data = {};
    data.visitor_id = visitor_id;
    data.website_token = website_token;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/visitor/initialize';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
	ajax.success = function(response)
	{
        var error = response.error;
        if(error != 0)
        {
            $('#app').html('invalid request');
            return;
        }

        visitor = response.visitor;
        chat_room = response.chat_room;
        chat_display = response.chat_display;

        ui_display();
        socket_io();
	}
    $.ajax(ajax);
}

function ui_display()
{
    var style_header = '';
    style_header += 'style="';
    style_header += 'height: ' + chat_display.header_height + 'px;';
    style_header += 'background-color: ' + chat_display.header_background_color + ';';
    style_header += 'color: ' + chat_display.header_text_color + ';';
    style_header += 'font-size: ' + chat_display.header_font_size + 'px;';
    style_header += '"';

    var style_content = '';
    style_content += 'style="';
    style_content += 'background-color: ' + chat_display.content_background_color + ';';
    style_content += '"';

    var style_footer = '';
    style_footer += 'style="';
    style_footer += 'height: ' + chat_display.footer_height + 'px;';
    style_footer += 'background-color: ' + chat_display.textbox_background_color + ';';
    style_footer += 'border-top: ' + chat_display.footer_border + 'px solid ' + chat_display.footer_line_color + ';';
    style_footer += '"';

    var style_textbox = '';
    style_textbox += 'style="';
    style_textbox += 'background-color: ' + chat_display.textbox_background_color + ';';
    style_textbox += 'color: ' + chat_display.textbox_text_color + ';';
    style_textbox += 'font-size: ' + chat_display.textbox_font_size + 'px;';
    style_textbox += '"';

    var html = '';
    html += '<div id="header" class="header" ' + style_header + '>';
    html += chat_display.header_text;
    html += '</div>';
    html += '<div id="content" class="content" ' + style_content + '>';
    html += '</div>';
    html += '<div id="footer" class="footer" ' + style_footer + '>';
    html += '<textarea id="textbox" class="textbox" ' + style_textbox + ' placeholder="' + chat_display.placeholder_text + '" onkeyup="textbox_onkeyup(event)"></textarea>';
    html += '</div>';

    $('#app').html(html);
    $('body').append('<style>.textbox::placeholder{color:' + chat_display.placeholder_color + '}</style>')
    $('#header').css('height', chat_display.header_height + 'px');
    $('#content').css('height', chat_display.content_height + 'px');
    $('#footer').css('height', chat_display.footer_height + 'px');
    $('#textbox').css('height', chat_display.textbox_height + 'px');
    $('#header').css('line-height', chat_display.header_height + 'px');
}

function textbox_onkeyup(event)
{
    if(event.keyCode == 13)
    {
        textbox_submit();
    }
}

function textbox_submit()
{
    // get message
    var message = $('#textbox').val().trim();
    $('#textbox').val('');
    if(message.length == 0)
    {
        return;
    }

    var data = {};
    data.visitor_id = visitor.id;
    data.chat_room_id = chat_room.id;
    data.message = message;
    data = JSON.stringify(data);

    var ajax = {};
	ajax.url = app_url + '/visitor/chat/message';
	ajax.data = data;
	ajax.type = 'post';
	ajax.contentType = 'application/json; charset=utf-8';
	ajax.processData = false;
    $.ajax(ajax);
}

function chat_message(task)
{
    var style_visitor = '';
    style_visitor += 'style="';
    style_visitor += 'background-color: ' + chat_display.visitor_background_color + ';';
    style_visitor += 'color: ' + chat_display.visitor_text_color + ';';
    style_visitor += 'font-size: ' + chat_display.visitor_font_size + 'px;';
    style_visitor += '"';

    var style_agent = '';
    style_agent += 'style="';
    style_agent += 'background-color: ' + chat_display.agent_background_color + ';';
    style_agent += 'color: ' + chat_display.agent_text_color + ';';
    style_agent += 'font-size: ' + chat_display.agent_font_size + 'px;';
    style_agent += '"';

    var chat_message = task.chat_message;
    var html = '';

    if(chat_message.visitor_id.length != 0)
    {
        html += '<div id="chat_message_' + chat_message.id + '" class="visitor_chat">';
        html += '<div class="visitor_message" ' + style_visitor + '>';
        html += chat_message.message;
        // html += chat_message.created_time;
        html += '</div>';
        html += '</div>';
    }

    if(chat_message.agent_id.length != 0)
    {
        html += '<div id="chat_message_' + chat_message.id + '" class="agent_chat">';
        html += '<div class="agent_message" ' + style_agent + '>';
        html += chat_message.message;
        // html += chat_message.created_time;
        html += '</div>';
        html += '</div>';
    }
    $('#content').append(html);
    $('#content').scrollTop($('#content')[0].scrollHeight);
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