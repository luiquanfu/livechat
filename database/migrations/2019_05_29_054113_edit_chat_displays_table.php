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
            $table->dropColumn('admin_id');

            $table->string('owner_id', 20)->after('id')->default('');
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

            $table->index('owner_id');
        });

        Schema::table('websites', function (Blueprint $table)
        {
            $table->dropColumn('admin_id');
            $table->string('owner_id', 20)->after('id')->default('');
            
            $table->index('owner_id');
        });

        Schema::table('admins', function (Blueprint $table)
        {
            $table->string('owner_id', 20)->after('id')->default('');
            $table->string('image', 25)->after('owner_id')->default('');
            
            $table->index('owner_id');
        });

        Schema::create('admin_admins', function (Blueprint $table)
        {
            $table->string('id', 20);
            $table->string('owner_id', 20)->default('');
            $table->string('admin_id', 20)->default('');
            $table->integer('created_at')->default(0);
            $table->integer('updated_at')->default(0);
            $table->integer('deleted_at')->default(0);
            
            $table->primary('id');
            $table->index('owner_id');
            $table->index('admin_id');
            $table->index('deleted_at');
        });

        $data = array();
        $data['owner_id'] = time().strtoupper(str_random(10));
        $query = DB::table('admins');
        $query->where('email', 'quanfu@hotmail.sg');
        $query->update($data);
    }

    public function down()
    {
        Schema::dropIfExists('admin_admins');

        Schema::table('admins', function (Blueprint $table)
        {
            $table->dropColumn('owner_id');
            $table->dropColumn('image');
        });

        Schema::table('websites', function (Blueprint $table)
        {
            $table->dropColumn('owner_id');
            $table->string('admin_id', 20)->default('');
        });

        Schema::table('chat_displays', function (Blueprint $table)
        {
            $table->dropColumn('owner_id');
            $table->string('admin_id', 20)->default('');
            
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
