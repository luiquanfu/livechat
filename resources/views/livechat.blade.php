livechat_init();

function livechat_init()
{
    var html = '';

    // icono
    html += '<link rel="stylesheet" href="{{ $app_url }}/assets/visitor/icono.min.css">';

    // livechat_frame
    html += '<iframe id="livechat_frame" scrolling="no" src="{{ $app_url }}/visitor/{{ $website_token }}" style="position: fixed; right: {{ $chat_display->body_right }}px; bottom: {{ $chat_display->body_bottom + 70 }}px; height: {{ $chat_display->body_height }}px; width: {{ $chat_display->body_width }}px; border-radius: 10px; background: none transparent; border: 1px solid {{ $chat_display->body_border_color }}; z-index: 99999999;"></iframe>';
    
    // livechat_open
    html += '<div id="livechat_open" style="position: fixed; right: {{ $chat_display->body_right }}px; bottom: {{ $chat_display->body_bottom }}px; height: 60px; width: 60px; cursor: pointer; z-index: 99999999;" onclick="livechat_open()">';
    html += '<div style="background-color: {{ $chat_display->header_background_color }}; color: {{ $chat_display->header_text_color }}; height: 60px; width: 60px; border-radius: 30px;">';
    html += '<i class="icono-comment" style="margin: 18px 0px 0px 15px;"></i>';
    html += '</div>';
    html += '</div>';

    // livechat_close
    html += '<div id="livechat_close" style="position: fixed; right: {{ $chat_display->body_right }}px; bottom: {{ $chat_display->body_bottom }}px; height: 60px; width: 60px; cursor: pointer; z-index: 99999999;" onclick="livechat_close()">';
    html += '<div style="background-color: {{ $chat_display->header_background_color }}; color: {{ $chat_display->header_text_color }}; height: 60px; width: 60px; border-radius: 30px;">';
    html += '<i class="icono-cross" style="zoom: 1.5; margin: 4px 0px 0px 5px;"></i>';
    html += '</div>';
    html += '</div>';

    var element = document.createElement('div');
    element.innerHTML = html;
    document.querySelector('body').appendChild(element);
    livechat_close();
}

function livechat_open()
{
    document.getElementById('livechat_frame').style.display = 'block';
    document.getElementById('livechat_open').style.display = 'none';
    document.getElementById('livechat_close').style.display = 'block';
}

function livechat_close()
{
    document.getElementById('livechat_frame').style.display = 'none';
    document.getElementById('livechat_open').style.display = 'block';
    document.getElementById('livechat_close').style.display = 'none';
}