<?php

namespace App\Http\Controllers\Visitor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatMessage extends Controller
{
    public function add(Request $request)
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

        // get chat_room
        $query = \DB::connection('mysql')->table('chat_rooms');
        $query->select('id', 'website_id');
        $query->where('id', $chat_room_id);
        $query->where('visitor_id', $visitor_id);
        $query->where('deleted_at', 0);
        $chat_room = $query->first();

        // if chat_room not found
        if($chat_room == null)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Chat room not found';
            return $response;
        }

        // create chat_message
        $chat_message = (object)[];
        $chat_message->id = $this->unique_id();
        $chat_message->admin_id = '';
        $chat_message->visitor_id = $visitor->id;
        $chat_message->name = $visitor->firstname;
        $chat_message->message = $message;
        $chat_message->created_at = time();
        $chat_message->created_date = date('d M Y', $chat_message->created_at);
        $chat_message->created_time = date('g:i A', $chat_message->created_at);

        // insert chat_message
        $data = array();
        $data['id'] = $chat_message->id;
        $data['website_id'] = $chat_room->website_id;
        $data['chat_room_id'] = $chat_room->id;
        $data['visitor_id'] = $chat_message->visitor_id;
        $data['admin_id'] = $chat_message->admin_id;
        $data['name'] = $chat_message->name;
        $data['message'] = $chat_message->message;
        $data['created_at'] = $chat_message->created_at;
        $data['updated_at'] = $chat_message->created_at;
        \DB::connection('mysql')->table('chat_messages')->insert($data);

        // get chat_room_admins
        $query = \DB::connection('mysql')->table('chat_room_admins');
        $query->select('admin_id');
        $query->where('chat_room_id', $chat_room->id);
        $query->where('deleted_at', 0);
        $chat_room_admins = $query->get();

        // send chat_message to chat_room_admins
        $task = (object)[];
        $task->action = 'chat_message';
        $task->chat_message = $chat_message;
        foreach($chat_room_admins as $chat_room_admin)
        {
            $data = array();
            $data['socket_id'] = $chat_room_admin->admin_id;
            $data['task'] = $task;
            \Redis::publish('admin', json_encode($data));
        }

        // get website_admins
        $query = \DB::connection('mysql')->table('website_admins');
        $query->select('admin_id');
        $query->where('website_id', $chat_room->website_id);
        $query->where('deleted_at', 0);
        $website_admins = $query->get();

        // send chat_room to website_admins who are not in chat_room_admins
        $chat_room_admin_ids = array_column($chat_room_admins->toArray(), 'admin_id');
        $task = (object)[];
        $task->action = 'chat_room';
        $task->chat_room = $chat_room;
        foreach($website_admins as $website_admin)
        {
            if(in_array($website_admin->admin_id, $chat_room_admin_ids) == null)
            {
                $data = array();
                $data['socket_id'] = $website_admin->admin_id;
                $data['task'] = $task;
                \Redis::publish('admin', json_encode($data));
            }
        }

        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        return $response;
    }
}
