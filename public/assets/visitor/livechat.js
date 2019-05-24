initialize();

function initialize()
{
    var html = '';
    html += '<iframe id="iframe_livechat" scrolling="no" src="http://local.luiquanfu.com/visitor/123" style="position: fixed; right: 50px; bottom: 50px; height: 520px; width: 350px; border: none; background: none transparent; transition: none 0s ease 0s; visibility: visible; z-index: 99999999 "></iframe>';
    html += '<div id="div_livechat" style="position: fixed; right: 50px; bottom: 50px; z-index: 99999999;" onclick="open_chat()"><h1>HEY<span class="icon-bubble"></h1></div>';
    $('body').append(html);
    $('#iframe_livechat').hide();
}

function open_chat()
{
    console.log('showing live chat');
    $('#div_livechat').hide();
    $('#iframe_livechat').show();
}