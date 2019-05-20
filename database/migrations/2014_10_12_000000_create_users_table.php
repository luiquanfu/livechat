<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table)
        {
            $table->string('id', 20);
            $table->string('firstname', 50)->default('');
            $table->string('lastname', 50)->default('');
            $table->string('email', 70)->default('');
            $table->string('mobile_country', 5)->default('');
            $table->string('mobile_number', 15)->default('');
            $table->string('ip_address', 25)->default('');
            $table->string('user_agent')->default('');
            $table->integer('created_at')->default(0);
            $table->integer('updated_at')->default(0);
            $table->integer('deleted_at')->default(0);

            $table->primary('id');
            $table->index('firstname');
            $table->index('lastname');
            $table->index('email');
            $table->index('mobile_number');
            $table->index('deleted_at');
        });

        Schema::create('admins', function (Blueprint $table)
        {
            $table->string('id', 20);
            $table->string('firstname', 50)->default('');
            $table->string('lastname', 50)->default('');
            $table->string('email', 70)->default('');
            $table->string('mobile_country', 5)->default('');
            $table->string('mobile_number', 15)->default('');
            $table->string('password')->default('');
            $table->string('last_visit')->default('');
            $table->integer('created_at')->default(0);
            $table->integer('updated_at')->default(0);
            $table->integer('deleted_at')->default(0);

            $table->primary('id');
            $table->index('firstname');
            $table->index('lastname');
            $table->index('email');
            $table->index('mobile_number');
            $table->index('deleted_at');
        });

        Schema::create('admin_tokens', function (Blueprint $table)
        {
            $table->string('id', 20);
            $table->string('admin_id', 20)->default('');
            $table->string('device_id', 20)->default('');
            $table->string('device_type', 10)->default('');
            $table->string('ip_address', 25)->default('');
            $table->string('user_agent')->default('');
            $table->string('api_token', 32)->default('');
            $table->integer('created_at')->default(0);
            $table->integer('updated_at')->default(0);
            $table->integer('deleted_at')->default(0);

            $table->primary('id');
            $table->index('admin_id');
            $table->index('device_id');
            $table->index('api_token');
            $table->index('deleted_at');
        });

        Schema::create('websites', function (Blueprint $table)
        {
            $table->string('id', 20);
            $table->string('admin_id', 20)->default('');
            $table->string('name', 30)->default('');
            $table->string('url', 50)->default('');
            $table->string('api_token', 32)->default('');
            $table->integer('created_at')->default(0);
            $table->integer('updated_at')->default(0);
            $table->integer('deleted_at')->default(0);
            
            $table->primary('id');
            $table->index('admin_id');
            $table->index('name');
            $table->index('url');
            $table->index('deleted_at');
        });

        Schema::create('operating_hours', function (Blueprint $table)
        {
            $table->string('id', 20);
            $table->string('website_id', 20)->default('');
            $table->integer('day')->default(0);
            $table->time('open_time');
            $table->time('close_time');
            $table->integer('created_at')->default(0);
            $table->integer('updated_at')->default(0);
            $table->integer('deleted_at')->default(0);
            
            $table->primary('id');
            $table->index('website_id');
            $table->index('deleted_at');
        });

        Schema::create('chat_displays', function (Blueprint $table)
        {
            $table->string('id', 20);
            $table->string('admin_id', 20)->default('');
            $table->string('name', 30)->default('');
            $table->integer('created_at')->default(0);
            $table->integer('updated_at')->default(0);
            $table->integer('deleted_at')->default(0);
            
            $table->primary('id');
            $table->index('admin_id');
            $table->index('deleted_at');
        });

        Schema::create('chat_rooms', function (Blueprint $table)
        {
            $table->string('id', 20);
            $table->string('visitor_id', 20)->default('');
            $table->string('website_id', 20)->default('');
            $table->integer('closed_at')->default(0);
            $table->integer('created_at')->default(0);
            $table->integer('updated_at')->default(0);
            $table->integer('deleted_at')->default(0);
            
            $table->primary('id');
            $table->index('visitor_id');
            $table->index('website_id');
            $table->index('closed_at');
            $table->index('deleted_at');
        });

        Schema::create('chat_room_admins', function (Blueprint $table)
        {
            $table->string('id', 20);
            $table->string('chat_room_id', 20)->default('');
            $table->string('admin_id', 20)->default('');
            $table->integer('created_at')->default(0);
            $table->integer('updated_at')->default(0);
            $table->integer('deleted_at')->default(0);
            
            $table->primary('id');
            $table->index('chat_room_id');
            $table->index('admin_id');
            $table->index('deleted_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('visitors');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('admin_tokens');
        Schema::dropIfExists('websites');
        Schema::dropIfExists('operating_hours');
        Schema::dropIfExists('chat_displays');
        Schema::dropIfExists('chat_rooms');
        Schema::dropIfExists('chat_room_admins');
    }
}
