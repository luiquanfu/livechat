<?php

namespace App\Http\Controllers\Visitor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Chat extends Controller
{
    public function message(Request $request)
    {
        // set variables
        $visitor_id = $request->get('visitor_id');
        $chat_room_id = $request->get('chat_room_id');
        $message = trim($request->get('message'));

        \Log::info('Visitor '.$visitor_id.' chat message '.$message);

        // if message is empty
        if(strlen($message) == 0)
        {
            return;
        }

        // get visitor
        $query = \DB::connection('mysql')->table('visitors');
        $query->select('id', 'firstname');
        $query->where('id', $visitor_id);
        $query->where('deleted_at', 0);
        $visitor = $query->first();

        // create chat_message
        $chat_message = (object)[];
        $chat_message->id = $this->unique_id();
        $chat_message->admin_id = '';
        $chat_message->visitor_id = $visitor->id;
        $chat_message->admin_name = '';
        $chat_message->visitor_name = $visitor->firstname;
        $chat_message->created_time = '1:50 PM';
        $chat_message->message = $message;

        // create task
		$task = (object)[];
		$task->chat_message = $chat_message;

		// notify user
		$data = array();
		$data['socket_id'] = $visitor_id;
		$data['action'] = 'chat_message';
		$data['task'] = $task;
        \Redis::publish('visitor', json_encode($data));

        // simulate admin response
        $chat_message = (object)[];
        $chat_message->id = $this->unique_id();
        $chat_message->admin_id = '123';
        $chat_message->visitor_id = '';
        $chat_message->admin_name = 'Test';
        $chat_message->visitor_name = '';
        $chat_message->created_time = '1:50 PM';
        $chat_message->message = 'This is a test respond from the admin';
		$task = (object)[];
		$task->chat_message = $chat_message;
		$data = array();
		$data['socket_id'] = $visitor_id;
		$data['action'] = 'chat_message';
		$data['task'] = $task;
        \Redis::publish('visitor', json_encode($data));
    }
}
