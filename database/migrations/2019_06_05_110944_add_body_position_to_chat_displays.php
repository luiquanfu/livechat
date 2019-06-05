<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBodyPositionToChatDisplays extends Migration
{
    public function up()
    {
        Schema::table('chat_displays', function (Blueprint $table)
        {
            $table->dropColumn('placeholder_text');
            $table->integer('body_bottom')->default(0)->after('body_width');
            $table->integer('body_right')->default(0)->after('body_width');
        });

        Schema::table('websites', function (Blueprint $table)
        {
            $table->string('chat_display_id', 20)->default('')->after('owner_id');
        });
    }

    public function down()
    {
        Schema::table('chat_displays', function (Blueprint $table)
        {
            $table->integer('placeholder_text');
            $table->dropColumn('body_bottom');
            $table->dropColumn('body_right');
        });

        Schema::table('websites', function (Blueprint $table)
        {
            $table->dropColumn('chat_display_id');
        });
    }
}
