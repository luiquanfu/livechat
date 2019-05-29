<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Admin extends Controller
{
    public function listing(Request $request)
    {
        // set variables
        $api_token = $request->get('api_token');
        $page = $request->get('page');
        $sort = $request->get('sort');
        $direction = $request->get('direction');
        $filter_firstname = $request->get('filter_firstname');
        $paginate = 10;

        \Log::info('Admin '.$api_token.' list admin page '.$page);

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }

        $admin = $response['admin'];

        // update admin
        $last_visit = array();
        $last_visit['page'] = 'admin_listing';
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

        // get admins
        $query = \DB::connection('mysql')->table('admins');
        $select = array();
        $select[] = 'id';
        $select[] = 'name';
        $query->select($select);
        $total_admins = $query->count();
        if(strlen($filter_firstname) != 0)
        {
            $query->where('name', 'like', '%'.$filter_firstname.'%');
        }
        $query->where('deleted_at', 0);
        $query->orderBy($sort, $direction);
        $query->paginate($paginate);
        $admins = $query->get();

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        $response['admins'] = $admins;
        $response['total_admins'] = $total_admins;
        $response['total_pages'] = ceil($total_admins / $paginate);
        $response['current_page'] = $page;
        return $response;
    }

    public function add(Request $request)
    {
        // set variables
        $api_token = $request->get('api_token');
        $name = $request->get('name');

        \Log::info('Admin '.$api_token.' add admin');

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
            $response['message'] = 'Building type name is required';
            return $response;
        }

        // insert admin
        $admin_id = $this->unique_id();
        $data = array();
        $data['id'] = $admin_id;
        $data['name'] = $name;
        $data['created_at'] = time();
        $data['updated_at'] = time();
        \DB::connection('mysql')->table('admins')->insert($data);

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
        $admin_id = $request->get('admin_id');

        \Log::info('Admin '.$api_token.' edit admin '.$admin_id);

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }

        // get admin
        $query = \DB::connection('mysql')->table('admins');
        $select = array();
        $select[] = 'id';
        $select[] = 'name';
        $query->select($select);
        $query->where('id', $admin_id);
        $admin = $query->first();

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        $response['admin'] = $admin;
        return $response;
    }

    public function update(Request $request)
    {
        // set variables
        $api_token = $request->get('api_token');
        $admin_id = $request->get('admin_id');
        $name = $request->get('name');

        \Log::info('Admin '.$api_token.' update admin '.$admin_id);

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
            $response['message'] = 'Building type name is required';
            return $response;
        }

        // update admin
        $data = array();
        $data['name'] = $name;
        $data['updated_at'] = time();
        \DB::connection('mysql')->table('admins')->where('id', $admin_id)->update($data);

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
        $admin_id = $request->get('admin_id');

        \Log::info('Admin '.$api_token.' destroy admin '.$admin_id);

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }

        // delete admin
        $data = array();
        $data['deleted_at'] = time();
        \DB::connection('mysql')->table('admins')->where('id', $admin_id)->update($data);

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        return $response;
    }
}
