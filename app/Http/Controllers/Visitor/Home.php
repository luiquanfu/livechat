<?php

namespace App\Http\Controllers\Visitor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Home extends Controller
{
    public function index($api_token)
    {
        // get visitor_id
        $visitor_id = $this->unique_id();
        if(isset($_COOKIE['visitor_id']))
        {
            $visitor_id = $_COOKIE['visitor_id'];
        }
        setcookie('visitor_id', $visitor_id, time() + (60 * 60 * 24 * 365 * 1), '/');

        \Log::info('Visitor '.$visitor_id.' home website '.$api_token);

        // get visitor
        $query = \DB::connection('mysql')->table('visitors');
        $select = array();
        $select[] = 'id';
        $select[] = 'firstname';
        $select[] = 'lastname';
        $select[] = 'email';
        $select[] = 'mobile_country';
        $select[] = 'mobile_number';
        $query->select($select);
        $query->where('id', $visitor_id);
        $visitor = $query->first();

        // if visitor not found
        if($visitor == null)
        {
            // create visitor
            $visitor = (object)[];
            $visitor->id = $visitor_id;
            $visitor->firstname = '';
            $visitor->lastname = '';
            $visitor->email = '';
            $visitor->mobile_country = '';
            $visitor->mobile_number = '';

            // insert visitor
            $data = array();
            $data['id'] = $visitor->id;
            $data['firstname'] = $visitor->firstname;
            $data['lastname'] = $visitor->lastname;
            $data['email'] = $visitor->email;
            $data['mobile_country'] = $visitor->mobile_country;
            $data['mobile_number'] = $visitor->mobile_number;
            $data['created_at'] = time();
            $data['updated_at'] = time();
            \DB::table('visitors')->insert($data);
        }

        // response
        $data = array();
        $data['app_url'] = config('app.url');
        $data['nodejs_url'] = config('app.nodejs_url');
        $data['visitor'] = $visitor;
        return view('visitor', $data);
    }

    public function message(Request $request)
    {
        // set variables
        $visitor_id = $request->get('visitor_id');
        $message = $request->get('message');

        \Log::info('Visitor '.$visitor_id.' '.$message);

        // create task
		$task = (object)[];
		$task->created_at = time();
		$task->message = $message;

		// notify user
		$data = array();
		$data['socket_id'] = $visitor_id;
		$data['action'] = 'chat_message';
		$data['task'] = $task;
        \Redis::publish('visitor', json_encode($data));
    }
}
