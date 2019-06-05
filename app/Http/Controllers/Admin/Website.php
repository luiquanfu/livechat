<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Website extends Controller
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

        \Log::info('Admin '.$api_token.' list website page '.$page);

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }

        $admin = $response['admin'];

        // update admin
        $last_visit = array();
        $last_visit['page'] = 'website_listing';
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
        $sorts[] = 'name';
        $sorts[] = 'url';
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

        // get website
        $query = \DB::connection('mysql')->table('websites');
        $select = array();
        $select[] = 'id';
        $select[] = 'name';
        $select[] = 'url';
        $query->select($select);
        $total_website = $query->count();
        if(strlen($filter_name) != 0)
        {
            $query->where('name', 'like', '%'.$filter_name.'%');
        }
        $query->where('owner_id', $admin->owner_id);
        $query->where('deleted_at', 0);
        $query->orderBy($sort, $direction);
        $query->paginate($paginate);
        $websites = $query->get();

        // modify websites
        foreach($websites as $website)
        {
            if(strpos($website->url, 'http') === false)
            {
                $website->url = 'http://'.$website->url;
            }
        }

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        $response['websites'] = $websites;
        $response['total_website'] = $total_website;
        $response['total_pages'] = ceil($total_website / $paginate);
        $response['current_page'] = $page;
        return $response;
    }

    public function add(Request $request)
    {
        // set variables
        $api_token = $request->get('api_token');
        $name = $request->get('name');
        $url = $request->get('url');
        $new_operating_hours = $request->get('new_operating_hours');

        \Log::info('Admin '.$api_token.' add website');

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
            $response['message'] = 'Website name is required';
            return $response;
        }

        // validate url
        if(strlen($url) == 0)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Website url is required';
            return $response;
        }

        // insert website
        $website_id = $this->unique_id();
        $data = array();
        $data['id'] = $website_id;
        $data['owner_id'] = $admin->owner_id;
        $data['name'] = $name;
        $data['url'] = $url;
        $data['api_token'] = $this->unique_token();
        $data['created_at'] = time();
        $data['updated_at'] = time();
        \DB::connection('mysql')->table('websites')->insert($data);

        // insert website_admin
        $data = array();
        $data['id'] = $this->unique_id();
        $data['website_id'] = $website_id;
        $data['admin_id'] = $admin->id;
        $data['created_at'] = time();
        $data['updated_at'] = time();
        \DB::connection('mysql')->table('website_admins')->insert($data);

        // insert operating_hours
        foreach($new_operating_hours as $operating_hour)
        {
            $data = array();
            $data['id'] = $this->unique_id();
            $data['website_id'] = $website_id;
            $data['day'] = $operating_hour['day'];
            $data['open_time'] = date_create_from_format('g:i A', $operating_hour['open_time'])->format('H:i:s');
            $data['close_time'] = date_create_from_format('g:i A', $operating_hour['close_time'])->format('H:i:s');
            $data['created_at'] = time();
            $data['updated_at'] = time();
            \DB::connection('mysql')->table('operating_hours')->insert($data);
        }

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
        $website_id = $request->get('website_id');

        \Log::info('Admin '.$api_token.' edit website '.$website_id);

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }
        $admin = $response['admin'];

        // get website
        $query = \DB::connection('mysql')->table('websites');
        $select = array();
        $select[] = 'id';
        $select[] = 'chat_display_id';
        $select[] = 'name';
        $select[] = 'url';
        $select[] = 'api_token';
        $query->select($select);
        $query->where('id', $website_id);
        $query->where('owner_id', $admin->owner_id);
        $query->where('deleted_at', 0);
        $website = $query->first();

        // if website not found
        if($website == null)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Website not found';
            return $response;
        }

        // get chat_displays
        $query = \DB::connection('mysql')->table('chat_displays');
        $select = array();
        $select[] = 'id';
        $select[] = 'name';
        $query->select($select);
        $query->where('owner_id', $admin->owner_id);
        $query->where('deleted_at', 0);
        $chat_displays = $query->get();

        // get operating_hours
        $query = \DB::connection('mysql')->table('operating_hours');
        $select = array();
        $select[] = 'id';
        $select[] = 'day';
        $select[] = 'open_time';
        $select[] = 'close_time';
        $query->select($select);
        $query->where('website_id', $website->id);
        $query->where('deleted_at', 0);
        $operating_hours = $query->get();

        // modify operating_hours
        foreach($operating_hours as $operating_hour)
        {
            $operating_hour->open_time = date('g:i A', strtotime($operating_hour->open_time));
            $operating_hour->close_time = date('g:i A', strtotime($operating_hour->close_time));
        }

        // modify website
        $website->javascript_url = config('app.url').'/livechat/'.$website->api_token.'.js';

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        $response['website'] = $website;
        $response['chat_displays'] = $chat_displays;
        $response['operating_hours'] = $operating_hours;
        return $response;
    }

    public function token(Request $request)
    {
        // set variables
        $api_token = $request->get('api_token');
        $website_id = $request->get('website_id');

        \Log::info('Admin '.$api_token.' token website '.$website_id);

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }
        $admin = $response['admin'];

        // update website
        $data = array();
        $data['api_token'] = $this->unique_token();
        $data['updated_at'] = time();
        $query = \DB::connection('mysql')->table('websites');
        $query->where('id', $website_id);
        $query->where('owner_id', $admin->owner_id);
        $query->where('deleted_at', 0);
        $query->update($data);

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        return $response;
    }

    public function update(Request $request)
    {
        // set variables
        $api_token = $request->get('api_token');
        $website_id = $request->get('website_id');
        $chat_display_id = $request->get('chat_display_id');
        $name = $request->get('name');
        $url = $request->get('url');
        $new_operating_hours = $request->get('new_operating_hours');
        $edit_operating_hours = $request->get('edit_operating_hours');

        \Log::info('Admin '.$api_token.' update website '.$website_id);

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
            $response['message'] = 'Website name is required';
            return $response;
        }

        // update website
        $data = array();
        $data['chat_display_id'] = $chat_display_id;
        $data['name'] = $name;
        $data['url'] = $url;
        $data['updated_at'] = time();
        \DB::connection('mysql')->table('websites')->where('id', $website_id)->update($data);

        // insert operating_hours
        foreach($new_operating_hours as $operating_hour)
        {
            $data = array();
            $data['id'] = $this->unique_id();
            $data['website_id'] = $website_id;
            $data['day'] = $operating_hour['day'];
            $data['open_time'] = date_create_from_format('g:i A', $operating_hour['open_time'])->format('H:i:s');
            $data['close_time'] = date_create_from_format('g:i A', $operating_hour['close_time'])->format('H:i:s');
            $data['created_at'] = time();
            $data['updated_at'] = time();
            \DB::connection('mysql')->table('operating_hours')->insert($data);
        }

        // update operating_hours
        foreach($edit_operating_hours as $operating_hour)
        {
            $data = array();
            $data['day'] = $operating_hour['day'];
            $data['open_time'] = date_create_from_format('g:i A', $operating_hour['open_time'])->format('H:i:s');
            $data['close_time'] = date_create_from_format('g:i A', $operating_hour['close_time'])->format('H:i:s');
            $data['updated_at'] = time();
            \DB::connection('mysql')->table('operating_hours')->where('id', $operating_hour['id'])->update($data);
        }

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
        $website_id = $request->get('website_id');

        \Log::info('Admin '.$api_token.' destroy website '.$website_id);

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }

        // delete website
        $data = array();
        $data['deleted_at'] = time();
        $query = \DB::connection('mysql')->table('websites');
        $query->where('id', $website_id);
        $query->where('deleted_at', 0);
        $query->update($data);

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        return $response;
    }
}
