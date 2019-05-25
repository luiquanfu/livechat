<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Home extends Controller
{
    public function index()
    {
        // get cookie
        $api_token = '';
        if(isset($_COOKIE['admin_token']))
        {
            $api_token = $_COOKIE['admin_token'];
        }

        \Log::info('Admin '.$api_token.' home');

        // get device_id
        $device_id = $this->unique_id();
        if(isset($_COOKIE['device_id']))
        {
            $device_id = $_COOKIE['device_id'];
        }
        setcookie('device_id', $device_id, time() + (60 * 60 * 24 * 365 * 1), '/');

        // response
        $data = array();
        $data['app_url'] = config('app.url');
        $data['nodejs_url'] = config('app.nodejs_url');
        $data['api_token'] = $api_token;
        $data['device_id'] = $device_id;
        return view('admin', $data);
    }

    public function initialize(Request $request)
    {
        // set variables
        $api_token = $request->get('api_token');

        \Log::info('Admin '.$api_token.' initialize');

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }

        $admin = $response['admin'];

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        $response['admin'] = $admin;
        return $response;
    }

    public function login(Request $request)
    {
        // set variables
        $email = $request->get('email');
        $password = $request->get('password');
        $device_id = $request->get('device_id');
        $device_type = $request->get('device_type');

        \Log::info('Admin '.$email.' login device '.$device_id);

        // validate device_id
        if(strlen($device_id) == 0)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Device ID is invalid';
            return $response;
        }

        // validate device_type
        if(strlen($device_type) == 0)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Device type is invalid';
            return $response;
        }

        // get admin
        $query = \DB::connection('mysql')->table('admins');
        $query->select('id', 'password');
        $query->where('email', $email);
        $query->where('deleted_at', 0);
        $admin = $query->first();

        // if admin not found
        if($admin == null)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Email or password is invalid';
            return $response;
        }

        // if password not match
        if(\Hash::check($password, $admin->password) == false)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Email or password is invalid';
            return $response;
        }

        // get admin_token
        $query = \DB::connection('mysql')->table('admin_tokens');
        $query->select('id', 'api_token');
        $query->where('admin_id', $admin->id);
        $query->where('device_id', $device_id);
        $query->where('deleted_at', 0);
        $admin_token = $query->first();

        // if admin_token not found
        if($admin_token == null)
        {
            // create admin_token
            $admin_token = (object)[];
            $admin_token->id = $this->unique_id();
            $admin_token->api_token = $this->unique_token();
            
            // insert admin_token
            $data = array();
            $data['id'] = $admin_token->id;
            $data['admin_id'] = $admin->id;
            $data['device_id'] = $device_id;
            $data['device_type'] = $device_type;
            $data['ip_address'] = $this->ip_address();
            $data['user_agent'] = $this->user_agent();
            $data['api_token'] = $admin_token->api_token;
            $data['created_at'] = time();
            $data['updated_at'] = time();
            \DB::connection('mysql')->table('admin_tokens')->insert($data);
        }

        setcookie('admin_token', $admin_token->api_token, time() + (60 * 60 * 24 * 365 * 1), '/');

        // modify admin
        $admin->api_token = $admin_token->api_token;

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        $response['admin'] = $admin;
        return $response;
    }

    public function logout(Request $request)
    {
        // set variables
        $api_token = $request->get('api_token');

        \Log::info('Admin '.$api_token.' logout');

        // delete admin_token
        $data = array();
        $data['deleted_at'] = time();
        \DB::connection('mysql')->table('admin_tokens')->where('api_token', $api_token)->update($data);
        setcookie('admin_token', null, time() - 1, '/');

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        return $response;
    }

    public function test(Request $request)
    {
        // set variables
        $api_token = $request->get('api_token');

        \Log::info('Admin '.$api_token.' test');

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }

        $admin = $response['admin'];

        // create task
		$task = (object)[];
		$task->firstname = $admin->firstname;
		$task->created_at = 'heee hee';
		$task->message = 'hello';

		// notify user
		$data = array();
		$data['socket_id'] = $admin->id;
		$data['action'] = 'chat_message';
		$data['task'] = $task;
        \Redis::publish('admin', json_encode($data));
        
        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        return $response;
    }
}
