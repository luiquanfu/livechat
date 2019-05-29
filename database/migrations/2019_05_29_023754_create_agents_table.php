<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentsTable extends Migration
{
    public function up()
    {
        Schema::create('website_admins', function (Blueprint $table)
        {
            $table->string('id', 20);
            $table->string('website_id', 20)->default('');
            $table->string('admin_id', 20)->default('');
            $table->integer('created_at')->default(0);
            $table->integer('updated_at')->default(0);
            $table->integer('deleted_at')->default(0);
            
            $table->primary('id');
            $table->index('website_id');
            $table->index('admin_id');
            $table->index('deleted_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('website_admins');
    }
}
