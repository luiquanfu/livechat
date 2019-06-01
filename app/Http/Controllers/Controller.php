<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function ip_address()
    {
        $ip_address = $_SERVER['REMOTE_ADDR'];
        if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip_forward = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip_address = $ip_forward[0];
        }
        return $ip_address;
    }

    public function user_agent()
    {
        $user_agent = '';
        if(isset($_SERVER['HTTP_USER_AGENT']))
        {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        }
        return $user_agent;
    }

    public function unique_id()
    {
        $random = strtoupper(str_random(10));
        return time().$random;
    }

    public function unique_token()
    {
        $random = strtoupper(str_random(10));
        return md5(time().$random);
    }

    public function check_admin($api_token)
    {
        // get admin_token
        $query = \DB::connection('mysql')->table('admin_tokens');
        $query->select('admin_id');
        $query->where('api_token', $api_token);
        $query->where('deleted_at', 0);
        $admin_token = $query->first();

        // if admin_token not found
        if($admin_token == null)
        {
            \Log::info('Admin '.$api_token.' token not found');

            $response = array();
            $response['error'] = 99;
            $response['message'] = 'Admin token not found';
            return $response;
        }

        // get admin
        $query = \DB::connection('mysql')->table('admins');
        $query->select('id', 'owner_id', 'firstname', 'email', 'last_visit');
        $query->where('id', $admin_token->admin_id);
        $query->where('deleted_at', 0);
        $admin = $query->first();

        // if admin not found
        if($admin == null)
        {
            \Log::info('Admin '.$api_token.' not found');

            $response = array();
            $response['error'] = 99;
            $response['message'] = 'Admin not found';
            return $response;
        }

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        $response['admin_token'] = $admin_token;
        $response['admin'] = $admin;
        return $response;
    }

    function wrap_currency($amount, $symbol, $direction, $decimal, $negative, $comma)
    {
        // convert integer to decimal amount
        $amount = $amount / pow(10, $decimal);
        
        // convert number to string
        $string = number_format($amount, $decimal, '.', $comma);

        // handle negative number
        if($amount < 0)
        {
            if($negative == 'none')
            {
                $string = number_format($amount * -1, $decimal);
            }

            if($negative == 'minus')
            {
                $string = number_format($amount * -1, $decimal);
            }

            if($negative == 'bracket')
            {
                $string = '('.number_format($amount * -1, $decimal).')';
            }
        }
        
        // add currency symbol to string
        if($direction == 'left')
        {
            $string = $symbol.$string;
        }
        if($direction == 'right')
        {
            $string = $string.$symbol;
        }
        
        // if negative is minus
        if($amount < 0)
        {
            if($negative == 'minus')
            {
                $string = '-'.$string;
            }
        }
        
        // return
        return $string;
    }
}
