<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditChatDisplaysTable extends Migration
{
    public function up()
    {
        Schema::table('chat_displays', function (Blueprint $table)
        {
            $table->integer('admin_font_size')->default(0)->after('name');
            $table->string('admin_background_color', 10)->default('')->after('name');
            $table->string('admin_text_color', 10)->default('')->after('name');
            $table->string('placeholder_text', 50)->default('')->after('name');
            $table->string('placeholder_color', 10)->default('')->after('name');
            $table->integer('visitor_font_size')->default(0)->after('name');
            $table->string('visitor_background_color', 10)->default('')->after('name');
            $table->string('visitor_text_color', 10)->default('')->after('name');
            $table->integer('textbox_height')->default(0)->after('name');
            $table->string('textbox_text', 50)->default('')->after('name');
            $table->integer('textbox_font_size')->default(0)->after('name');
            $table->string('textbox_background_color', 10)->default('')->after('name');
            $table->string('textbox_text_color', 10)->default('')->after('name');
            $table->integer('content_height')->default(0)->after('name');
            $table->string('content_background_color', 10)->default('')->after('name');
            $table->integer('footer_height')->default(0)->after('name');
            $table->integer('footer_border')->default(0)->after('name');
            $table->string('footer_line_color', 10)->default('')->after('name');
            $table->integer('header_font_size')->default(0)->after('name');
            $table->string('header_text', 100)->default('')->after('name');
            $table->string('header_background_color', 10)->default('')->after('name');
            $table->string('header_text_color', 10)->default('')->after('name');
            $table->integer('header_height')->default(0)->after('name');
            $table->integer('body_width')->default(0)->after('name');
            $table->integer('body_height')->default(0)->after('name');
            $table->string('body_border_color', 10)->default('')->after('name');
        });

    }

    public function down()
    {
        Schema::table('chat_displays', function (Blueprint $table)
        {
            $table->dropColumn('admin_font_size');
            $table->dropColumn('admin_background_color');
            $table->dropColumn('admin_text_color');
            $table->dropColumn('placeholder_text');
            $table->dropColumn('placeholder_color');
            $table->dropColumn('visitor_font_size');
            $table->dropColumn('visitor_background_color');
            $table->dropColumn('visitor_text_color');
            $table->dropColumn('textbox_height');
            $table->dropColumn('textbox_text');
            $table->dropColumn('textbox_font_size');
            $table->dropColumn('textbox_background_color');
            $table->dropColumn('textbox_text_color');
            $table->dropColumn('content_height');
            $table->dropColumn('content_background_color');
            $table->dropColumn('footer_height');
            $table->dropColumn('footer_border');
            $table->dropColumn('footer_line_color');
            $table->dropColumn('header_font_size');
            $table->dropColumn('header_text');
            $table->dropColumn('header_background_color');
            $table->dropColumn('header_text_color');
            $table->dropColumn('header_height');
            $table->dropColumn('body_width');
            $table->dropColumn('body_height');
            $table->dropColumn('body_border_color');
        });
    }
}
