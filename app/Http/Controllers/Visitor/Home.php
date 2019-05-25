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

        $website_token = str_replace('.js', '', $website_token);
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
        $query->select('id');
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
        $chat_display = (object)[];
        $chat_display->body_border_color = '#ffffff';
        $chat_display->body_height = 520;
        $chat_display->body_width = 350;
        $chat_display->header_image = '';
        $chat_display->header_height = 50;
        $chat_display->footer_line_color = '#333333';
        $chat_display->footer_border = 1;
        $chat_display->footer_height = 119;
        $chat_display->header_text_color = '#ffffff';
        $chat_display->header_background_color = '#3c8dbc';
        $chat_display->header_text = 'We are online and ready to chat with you';
        $chat_display->header_font_size = 15;
        $chat_display->content_background_color = '#ffffff';
        $chat_display->content_height = 350;
        $chat_display->textbox_text_color = '#000000';
        $chat_display->textbox_background_color = '#ffffff';
        $chat_display->textbox_font_size = 15;
        $chat_display->textbox_text = 'Type your message';
        $chat_display->textbox_height = 84;
        $chat_display->visitor_text_color = '#ffffff';
        $chat_display->visitor_background_color = '#3c8dbc';
        $chat_display->visitor_font_size = 15;
        $chat_display->agent_text_color = '#ffffff';
        $chat_display->agent_background_color = '#dd4b39';
        $chat_display->agent_font_size = 15;
        $chat_display->placeholder_color = '#aaaaaa';
        $chat_display->placeholder_text = 'Type your message';

        // $chat_display->textbox_text_color = '#ffffff';
        // $chat_display->textbox_background_color = '#3c8dbc';

        // response
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        $response['visitor'] = $visitor;
        $response['chat_room'] = $chat_room;
        $response['chat_display'] = $chat_display;
        return $response;
    }
}
