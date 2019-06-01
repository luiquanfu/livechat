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

        // update chat_display
        $data = array();
        $data['name'] = $name;
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
        \DB::connection('mysql')->table('chat_displays')->where('id', $chat_display_id)->update($data);

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        return $response;
    }
}
