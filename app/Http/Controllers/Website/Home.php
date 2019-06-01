<?php

namespace App\Http\Controllers\Website;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Home extends Controller
{
    public function index()
    {
        return view('website');
    }

    public function test()
    {
        $admin_ids = array();
        $query = \DB::connection('mysql')->table('admins');
        $query->select('id');
        $admins = $query->get();
        $admin_ids = array_column($admins->toArray(), 'id');
        return json_encode($admin_ids);
    }
}
