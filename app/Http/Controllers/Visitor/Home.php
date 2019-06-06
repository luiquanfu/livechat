<?php

namespace App\Http\Controllers\Visitor;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Home extends Controller
{
    public function index($website_token)
    {
        // get visitor_id
        $visitor_id = $this->unique_id();
        if(isset($_COOKIE['visitor_id']))
        {
            $visitor_id = $_COOKIE['visitor_id'];
        }
        setcookie('visitor_id', $visitor_id, time() + (60 * 60 * 24 * 365 * 1), '/');

        \Log::info('Visitor '.$visitor_id.' website '.$website_token);

        // get visitor
        $query = \DB::connection('mysql')->table('visitors');
        $select = array();
        $select[] = 'id';
        $query->select($select);
        $query->where('id', $visitor_id);
        $query->where('deleted_at', 0);
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
            $data['ip_address'] = $this->ip_address();
            $data['user_agent'] = $this->user_agent();
            $data['created_at'] = time();
            $data['updated_at'] = time();
            \DB::connection('mysql')->table('visitors')->insert($data);
        }

        // response
        $data = array();
        $data['app_url'] = config('app.url');
        $data['nodejs_url'] = config('app.nodejs_url');
        $data['website_token'] = $website_token;
        $data['visitor_id'] = $visitor->id;
        return view('visitor', $data);
    }

    public function initialize(Request $request)
    {
        // set variables
        $visitor_id = $request->get('visitor_id');
        $website_token = $request->get('website_token');

        \Log::info('Visitor '.$visitor_id.' initialize '.$website_token);

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
        $query->where('deleted_at', 0);
        $visitor = $query->first();

        // if visitor not found
        if($visitor == null)
        {
            \Log::warning('Visitor '.$visitor_id.' not found');

            // response
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Visitor not found';
            return $response;
        }

        // get website
        $query = \DB::connection('mysql')->table('websites');
        $query->select('id', 'chat_display_id');
        $query->where('api_token', $website_token);
        $query->where('deleted_at', 0);
        $website = $query->first();
        if($website == null)
        {
            \Log::warning('Website '.$website_token.' not found');

            // response
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Website not found';
            return $response;
        }

        // get chat_room
        $query = \DB::connection('mysql')->table('chat_rooms');
        $query->select('id');
        $query->where('visitor_id', $visitor->id);
        $query->where('website_id', $website->id);
        $query->where('closed_at', 0);
        $query->where('deleted_at', 0);
        $chat_room = $query->first();

        // if chat_room not found
        if($chat_room == null)
        {
            // create chat_room
            $chat_room = (object)[];
            $chat_room->id = $this->unique_id();
            $chat_room->visitor_id = $visitor->id;
            $chat_room->website_id = $website->id;

            // insert chat_room
            $data = array();
            $data['id'] = $chat_room->id;
            $data['visitor_id'] = $chat_room->visitor_id;
            $data['website_id'] = $chat_room->website_id;
            $data['created_at'] = time();
            $data['updated_at'] = time();
            \DB::connection('mysql')->table('chat_rooms')->insert($data);
        }

        // get chat_display
        $query = \DB::connection('mysql')->table('chat_displays');
        $select = array();
        $select[] = 'header_height';
        $select[] = 'header_text_color';
        $select[] = 'header_background_color';
        $select[] = 'header_text';
        $select[] = 'header_font_size';
        $select[] = 'footer_line_color';
        $select[] = 'footer_border';
        $select[] = 'footer_height';
        $select[] = 'content_background_color';
        $select[] = 'content_height';
        $select[] = 'textbox_text_color';
        $select[] = 'textbox_background_color';
        $select[] = 'textbox_font_size';
        $select[] = 'textbox_text';
        $select[] = 'textbox_height';
        $select[] = 'placeholder_color';
        $select[] = 'visitor_text_color';
        $select[] = 'visitor_background_color';
        $select[] = 'visitor_font_size';
        $select[] = 'admin_text_color';
        $select[] = 'admin_background_color';
        $select[] = 'admin_font_size';
        $query->select($select);
        $query->where('id', $website->chat_display_id);
        $query->where('deleted_at', 0);
        $chat_display = $query->first();

        // response
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        $response['visitor'] = $visitor;
        $response['chat_room'] = $chat_room;
        $response['chat_display'] = $chat_display;
        return $response;
    }

    public function livechat($website_token)
    {
        $website_token = str_replace('.js', '', $website_token);
        \Log::info('Livechat '.$website_token.' launch');

        // get website
        $query = \DB::connection('mysql')->table('websites');
        $query->select('chat_display_id');
        $query->where('api_token', $website_token);
        $query->where('deleted_at', 0);
        $website = $query->first();

        // if website not found
        if($website == null)
        {
            return 'invalid request';
        }

        // get chat_display
        $query = \DB::connection('mysql')->table('chat_displays');
        $select = array();
        $select[] = 'body_border_color';
        $select[] = 'body_height';
        $select[] = 'body_width';
        $select[] = 'body_bottom';
        $select[] = 'body_right';
        $select[] = 'header_background_color';
        $select[] = 'header_text_color';
        $query->select($select);
        $query->where('id', $website->chat_display_id);
        $query->where('deleted_at', 0);
        $chat_display = $query->first();

        // if chat_display not found
        if($chat_display == null)
        {
            return 'invalid request';
        }

        $data = array();
        $data['app_url'] = config('app.url');
        $data['website_token'] = $website_token;
        $data['chat_display'] = $chat_display;
        $script = view('livechat', $data)->render();
        return \Response::make($script, 200)->header('Content-Type', 'application/javascript');
    }
}
