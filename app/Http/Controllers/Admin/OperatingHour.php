<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OperatingHour extends Controller
{
    public function destroy(Request $request)
    {
        // set variables
        $api_token = $request->get('api_token');
        $operating_hour_id = $request->get('operating_hour_id');

        \Log::info('Admin '.$api_token.' destroy operating_hour '.$operating_hour_id);

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }

        // delete operating_hour
        $data = array();
        $data['deleted_at'] = time();
        $query = \DB::connection('mysql')->table('operating_hours');
        $query->where('id', $operating_hour_id);
        $query->where('deleted_at', 0);
        $query->update($data);

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        return $response;
    }
}
