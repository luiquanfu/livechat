<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertDefaultChatDisplay extends Migration
{
    public function up()
    {
        $data = array();
        $data['id'] = 0;
        $data['owner_id'] = '';
        $data['name'] = 'Default';
        $data['body_border_color'] = '#ffffff';
        $data['body_height'] = 520;
        $data['body_width'] = 350;
        $data['body_right'] = 50;
        $data['body_bottom'] = 50;
        $data['header_height'] = 50;
        $data['header_text_color'] = '#ffffff';
        $data['header_background_color'] = '#3c8dbc';
        $data['header_text'] = 'We are online and ready to chat';
        $data['header_font_size'] = 14;
        $data['footer_line_color'] = '#dddddd';
        $data['footer_border'] = 1;
        $data['footer_height'] = 119;
        $data['content_background_color'] = '#ffffff';
        $data['content_height'] = 350;
        $data['textbox_text_color'] = '#000000';
        $data['textbox_background_color'] = '#ffffff';
        $data['textbox_font_size'] = '14';
        $data['textbox_text'] = 'Type your message';
        $data['textbox_height'] = 85;
        $data['visitor_text_color'] = '#ffffff';
        $data['visitor_background_color'] = '#3c8dbc';
        $data['visitor_font_size'] = 14;
        $data['placeholder_color'] = '#dddddd';
        $data['admin_text_color'] = '#ffffff';
        $data['admin_background_color'] = '#f39c12';
        $data['admin_font_size'] = 14;
        $data['created_at'] = time();
        $data['updated_at'] = time();
        DB::table('chat_displays')->insert($data);
    }

    public function down()
    {
        DB::table('chat_displays')->where('id', 0)->delete();
    }
}
