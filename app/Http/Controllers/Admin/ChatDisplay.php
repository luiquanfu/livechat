<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatDisplay extends Controller
{
    public function listing(Request $request)
    {
        // set variables
        $api_token = $request->get('api_token');
        $page = $request->get('page');
        $sort = $request->get('sort');
        $direction = $request->get('direction');
        $filter_name = $request->get('filter_name');
        $paginate = 10;

        \Log::info('Admin '.$api_token.' list chat_display page '.$page);

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }

        $admin = $response['admin'];

        // update admin
        $last_visit = array();
        $last_visit['page'] = 'chat_display_listing';
        $data = array();
        $data['last_visit'] = json_encode($last_visit);
        $data['updated_at'] = time();
        \DB::connection('mysql')->table('admins')->where('id', $admin->id)->update($data);

        // validate page
        if($page < 1)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Page is invalid';
            return $response;
        }

        // validate sort
        $sorts = array();
        $sorts[] = 'id';
        $sorts[] = 'name';
        $sorts[] = 'lock_period';
        if(in_array($sort, $sorts) == null)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Sort is invalid';
            return $response;
        }

        // validate direction
        $directions = array();
        $directions[] = 'asc';
        $directions[] = 'desc';
        if(in_array($direction, $directions) == null)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Direction is invalid';
            return $response;
        }

        // get chat_displays
        $query = \DB::connection('mysql')->table('chat_displays');
        $select = array();
        $select[] = 'id';
        $select[] = 'name';
        $query->select($select);
        $total_chat_displays = $query->count();
        if(strlen($filter_name) != 0)
        {
            $query->where('name', 'like', '%'.$filter_name.'%');
        }
        $query->where('owner_id', $admin->owner_id);
        $query->where('deleted_at', 0);
        $query->orderBy($sort, $direction);
        $query->paginate($paginate);
        $chat_displays = $query->get();

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        $response['chat_displays'] = $chat_displays;
        $response['total_chat_displays'] = $total_chat_displays;
        $response['total_pages'] = ceil($total_chat_displays / $paginate);
        $response['current_page'] = $page;
        return $response;
    }

    public function add(Request $request)
    {
        // set variables
        $api_token = $request->get('api_token');
        $name = $request->get('name');

        \Log::info('Admin '.$api_token.' add chat_display');

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }

        $admin = $response['admin'];

        // validate name
        if(strlen($name) == 0)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Chat display name is required';
            return $response;
        }

        // insert chat_display
        $chat_display_id = $this->unique_id();
        $data = array();
        $data['id'] = $chat_display_id;
        $data['owner_id'] = $admin->owner_id;
        $data['name'] = $name;
        $data['created_at'] = time();
        $data['updated_at'] = time();
        \DB::connection('mysql')->table('chat_displays')->insert($data);

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        return $response;
    }

    public function edit(Request $request)
    {
        // set variables
        $api_token = $request->get('api_token');
        $chat_display_id = $request->get('chat_display_id');

        \Log::info('Admin '.$api_token.' edit chat_display '.$chat_display_id);

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }

        // get chat_display
        $query = \DB::connection('mysql')->table('chat_displays');
        $select = array();
        $select[] = 'id';
        $select[] = 'name';
        $select[] = 'body_border_color';
        $select[] = 'body_height';
        $select[] = 'body_width';
        $select[] = 'body_bottom';
        $select[] = 'body_right';
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
        $query->where('id', $chat_display_id);
        $chat_display = $query->first();

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        $response['chat_display'] = $chat_display;
        return $response;
    }

    public function update(Request $request)
    {
        // set variables
        $api_token = $request->get('api_token');
        $chat_display_id = $request->get('chat_display_id');
        $name = $request->get('name');
        $body_border_color = $request->get('body_border_color');
        $body_height = $request->get('body_height');
        $body_width = $request->get('body_width');
        $body_bottom = $request->get('body_bottom');
        $body_right = $request->get('body_right');
        $header_height = $request->get('header_height');
        $header_text_color = $request->get('header_text_color');
        $header_background_color = $request->get('header_background_color');
        $header_text = $request->get('header_text');
        $header_font_size = $request->get('header_font_size');
        $footer_line_color = $request->get('footer_line_color');
        $footer_border = $request->get('footer_border');
        $footer_height = $request->get('footer_height');
        $content_background_color = $request->get('content_background_color');
        $textbox_text_color = $request->get('textbox_text_color');
        $textbox_background_color = $request->get('textbox_background_color');
        $textbox_font_size = $request->get('textbox_font_size');
        $textbox_text = $request->get('textbox_text');
        $textbox_height = $request->get('textbox_height');
        $placeholder_color = $request->get('placeholder_color');
        $visitor_text_color = $request->get('visitor_text_color');
        $visitor_background_color = $request->get('visitor_background_color');
        $visitor_font_size = $request->get('visitor_font_size');
        $admin_text_color = $request->get('admin_text_color');
        $admin_background_color = $request->get('admin_background_color');
        $admin_font_size = $request->get('admin_font_size');

        \Log::info('Admin '.$api_token.' update chat_display '.$chat_display_id);

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }

        // validate name
        if(strlen($name) == 0)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Chat display name is required';
            return $response;
        }

        // calculate
        $content_height = $body_height - $header_height - $footer_height - $footer_border;
        $textbox_height = $footer_height + $footer_border - 35;

        // update chat_display
        $data = array();
        $data['name'] = $name;
        $data['body_border_color'] = $body_border_color;
        $data['body_height'] = $body_height;
        $data['body_width'] = $body_width;
        $data['body_bottom'] = $body_bottom;
        $data['body_right'] = $body_right;
        $data['header_height'] = $header_height;
        $data['header_text_color'] = $header_text_color;
        $data['header_background_color'] = $header_background_color;
        $data['header_text'] = $header_text;
        $data['header_font_size'] = $header_font_size;
        $data['footer_line_color'] = $footer_line_color;
        $data['footer_border'] = $footer_border;
        $data['footer_height'] = $footer_height;
        $data['content_background_color'] = $content_background_color;
        $data['content_height'] = $content_height;
        $data['textbox_text_color'] = $textbox_text_color;
        $data['textbox_background_color'] = $textbox_background_color;
        $data['textbox_font_size'] = $textbox_font_size;
        $data['textbox_text'] = $textbox_text;
        $data['textbox_height'] = $textbox_height;
        $data['placeholder_color'] = $placeholder_color;
        $data['visitor_text_color'] = $visitor_text_color;
        $data['visitor_background_color'] = $visitor_background_color;
        $data['visitor_font_size'] = $visitor_font_size;
        $data['admin_text_color'] = $admin_text_color;
        $data['admin_background_color'] = $admin_background_color;
        $data['admin_font_size'] = $admin_font_size;
        $data['updated_at'] = time();
        \DB::connection('mysql')->table('chat_displays')->where('id', $chat_display_id)->update($data);

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        return $response;
    }

    public function destroy(Request $request)
    {
        // set variables
        $api_token = $request->get('api_token');
        $chat_display_id = $request->get('chat_display_id');

        \Log::info('Admin '.$api_token.' destroy chat_display '.$chat_display_id);

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }

        // delete chat_display
        $data = array();
        $data['deleted_at'] = time();
        $query = \DB::connection('mysql')->table('chat_displays');
        $query->where('id', $chat_display_id);
        $query->where('deleted_at', 0);
        $query->update($data);

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        return $response;
    }
}
