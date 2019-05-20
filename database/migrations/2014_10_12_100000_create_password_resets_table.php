<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasswordResetsTable extends Migration
{
    public function up()
    {
        $data = array();
        $data['id'] = $this->unique_id();
        $data['firstname'] = 'Quanfu';
        $data['lastname'] = 'Lui';
        $data['email'] = 'quanfu@hotmail.sg';
        $data['mobile_country'] = '65';
        $data['mobile_number'] = '90174663';
        $data['password'] = bcrypt('alienwarem14x');
        $data['created_at'] = time();
        $data['updated_at'] = time();
        DB::table('admins')->insert($data);
    }

    public function down()
    {
    }

    public function unique_id()
    {
        $random = strtoupper(str_random(10));
        return time().$random;
    }
}
