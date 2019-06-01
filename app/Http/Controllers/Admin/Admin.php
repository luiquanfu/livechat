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
        $sorts[] = 'firstname';
        $sorts[] = 'email';
        $sorts[] = 'mobile_number';
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

        // get admin_ids
        $admin_ids = array();
        $query = \DB::connection('mysql')->table('admin_admins');
        $query->select('admin_id');
        $query->where('owner_id', $admin->owner_id);
        $query->where('deleted_at', 0);
        $admin_admins = $query->get();
        $admin_ids = array_column($admin_admins->toArray(), 'admin_id');

        // get admins
        $query = \DB::connection('mysql')->table('admins');
        $select = array();
        $select[] = 'id';
        $select[] = 'image';
        $select[] = 'firstname';
        $select[] = 'lastname';
        $select[] = 'email';
        $select[] = 'mobile_country';
        $select[] = 'mobile_number';
        $query->select($select);
        $total_admins = $query->count();
        if(strlen($filter_firstname) != 0)
        {
            $query->where('name', 'like', '%'.$filter_firstname.'%');
        }
        $query->whereIn('id', $admin_ids);
        $query->where('deleted_at', 0);
        $query->orderBy($sort, $direction);
        $query->paginate($paginate);
        $admins = $query->get();

        // modify admins
        foreach($admins as $admin)
        {
            $image_url = config('app.url').'/assets/default/admin.jpg';
            if(strlen($admin->image) != 0)
            {
                $image_url = \Storage::disk('public')->url('images/admins/'.$admin->image);
            }
            $admin->image = $image_url;
        }

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
        $image = $request->get('image');
        $firstname = $request->get('firstname');
        $lastname = $request->get('lastname');
        $email = $request->get('email');
        $mobile_country = $request->get('mobile_country');
        $mobile_number = $request->get('mobile_number');
        $password = $request->get('password');

        \Log::info('Admin '.$api_token.' add admin');

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }
        $admin = $response['admin'];

        // validate firstname
        if(strlen($firstname) == 0)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'First name is required';
            return $response;
        }

        // validate lastname
        if(strlen($lastname) == 0)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Last name is required';
            return $response;
        }

        // validate email
        $result = \Validator::make(['email' => $email], ['email' => 'required|email']);
        if($result->fails())
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Email is invalid';
            return $response;
        }
        $query = \DB::connection('mysql')->table('admins');
        $query->select('id');
        $query->where('email', $email);
        $query->where('deleted_at', 0);
        $result = $query->first();
        if($result != null)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Email is already taken';
            return $response;
        }

        // validate mobile_country
        $result = \Validator::make(['mobile_country' => $mobile_country], ['mobile_country' => 'required|numeric']);
        if($result->fails())
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Country code is invalid';
            return $response;
        }

        // validate mobile_number
        $result = \Validator::make(['mobile_number' => $mobile_number], ['mobile_number' => 'required|numeric']);
        if($result->fails())
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Mobile number is invalid';
            return $response;
        }

        // validate password
        if(strlen($password) < 7)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Password should be at least 7 chars';
            return $response;
        }

        // image
        if(strlen($image) != 0)
        {
            $filename = $this->unique_id().'.jpg';
            $image = \Image::make(file_get_contents($image));
            $binary = $image->stream()->__toString();
            \Storage::disk('public')->put('images/admins/'.$filename, $binary);
            $image = $filename;
        }

        // insert admin
        $admin_id = $this->unique_id();
        $data = array();
        $data['id'] = $admin_id;
        $data['owner_id'] = '';
        $data['image'] = '';
        if(strlen($image) != 0)
        {
            $data['image'] = $image;
        }
        $data['firstname'] = $firstname;
        $data['lastname'] = $lastname;
        $data['email'] = $email;
        $data['mobile_country'] = $mobile_country;
        $data['mobile_number'] = $mobile_number;
        $data['password'] = bcrypt($password);
        $data['created_at'] = time();
        $data['updated_at'] = time();
        \DB::connection('mysql')->table('admins')->insert($data);

        // insert admin_admins
        $data = array();
        $data['id'] = $this->unique_id();
        $data['owner_id'] = $admin->owner_id;
        $data['admin_id'] = $admin_id;
        $data['created_at'] = time();
        $data['updated_at'] = time();
        \DB::connection('mysql')->table('admin_admins')->insert($data);

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
        $admin = $response['admin'];

        // get admin_admin
        $query = \DB::connection('mysql')->table('admin_admins');
        $query->select('id');
        $query->where('admin_id', $admin_id);
        $query->where('owner_id', $admin->owner_id);
        $query->where('deleted_at', 0);
        $admin_admin = $query->first();

        // if admin_admin not found
        if($admin_admin == null)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Admin not found';
            return $response;
        }

        // get admin
        $query = \DB::connection('mysql')->table('admins');
        $select = array();
        $select[] = 'id';
        $select[] = 'owner_id';
        $select[] = 'image';
        $select[] = 'firstname';
        $select[] = 'lastname';
        $select[] = 'email';
        $select[] = 'mobile_country';
        $select[] = 'mobile_number';
        $query->select($select);
        $query->where('id', $admin_id);
        $query->where('deleted_at', 0);
        $admin = $query->first();

        // if admin not found
        if($admin == null)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Admin not found';
            return $response;
        }

        // modify admin
        $image_url = config('app.url').'/assets/default/admin.jpg';
        if(strlen($admin->image) != 0)
        {
            $image_url = \Storage::disk('public')->url('images/admins/'.$admin->image);
        }
        $admin->image = $image_url;

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
        $image = $request->get('image');
        $firstname = $request->get('firstname');
        $lastname = $request->get('lastname');
        $email = $request->get('email');
        $mobile_country = $request->get('mobile_country');
        $mobile_number = $request->get('mobile_number');
        $password = $request->get('password');

        \Log::info('Admin '.$api_token.' update admin '.$admin_id);

        // validate api_token
        $response = $this->check_admin($api_token);
        if($response['error'] != 0)
        {
            return $response;
        }
        $admin = $response['admin'];

        // validate firstname
        if(strlen($firstname) == 0)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'First name is required';
            return $response;
        }

        // validate lastname
        if(strlen($lastname) == 0)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Last name is required';
            return $response;
        }

        // get admin_admin
        $query = \DB::connection('mysql')->table('admin_admins');
        $query->select('id');
        $query->where('admin_id', $admin_id);
        $query->where('owner_id', $admin->owner_id);
        $query->where('deleted_at', 0);
        $admin_admin = $query->first();

        // if admin_admin not found
        if($admin_admin == null)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Admin not found';
            return $response;
        }

        // get admin
        $query = \DB::connection('mysql')->table('admins');
        $query->select('image', 'email');
        $query->where('id', $admin_id);
        $query->where('deleted_at', 0);
        $admin = $query->first();

        // if admin not found
        if($admin == null)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Admin not found';
            return $response;
        }

        // validate email
        if($email != $admin->email)
        {
            $result = \Validator::make(['email' => $email], ['email' => 'required|email']);
            if($result->fails())
            {
                $response = array();
                $response['error'] = 1;
                $response['message'] = 'Email is invalid';
                return $response;
            }
            $query = \DB::connection('mysql')->table('admins');
            $query->select('id');
            $query->where('id', '!=', $admin_id);
            $query->where('email', $email);
            $query->where('deleted_at', 0);
            $result = $query->first();
            if($result != null)
            {
                $response = array();
                $response['error'] = 1;
                $response['message'] = 'Email is already taken';
                return $response;
            }
        }

        // validate mobile_country
        $result = \Validator::make(['mobile_country' => $mobile_country], ['mobile_country' => 'required|numeric']);
        if($result->fails())
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Country code is invalid';
            return $response;
        }

        // validate mobile_number
        $result = \Validator::make(['mobile_number' => $mobile_number], ['mobile_number' => 'required|numeric']);
        if($result->fails())
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Mobile number is invalid';
            return $response;
        }

        // validate password
        if(strlen($password) != 0)
        if(strlen($password) < 7)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Password should be at least 7 chars';
            return $response;
        }

        // change image
        if(strlen($image) != 0)
        {
            $filename = $this->unique_id().'.jpg';
            $image = \Image::make(file_get_contents($image));
            $binary = $image->stream()->__toString();
            \Storage::disk('public')->put('images/admins/'.$filename, $binary);
            $image = $filename;
            \Storage::disk('public')->delete('images/admins/'.$admin->image);
        }

        // update admin
        $data = array();
        if(strlen($image) != 0)
        {
            $data['image'] = $image;
        }
        $data['firstname'] = $firstname;
        $data['lastname'] = $lastname;
        $data['email'] = $email;
        $data['mobile_country'] = $mobile_country;
        $data['mobile_number'] = $mobile_number;
        if(strlen($password) != 0)
        {
            $data['password'] = bcrypt($password);
        }
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
        $admin = $response['admin'];

        // get admin_admin
        $query = \DB::connection('mysql')->table('admin_admins');
        $query->select('id');
        $query->where('admin_id', $admin_id);
        $query->where('owner_id', $admin->owner_id);
        $query->where('deleted_at', 0);
        $admin_admin = $query->first();

        // if admin_admin not found
        if($admin_admin == null)
        {
            $response = array();
            $response['error'] = 1;
            $response['message'] = 'Admin not found';
            return $response;
        }

        // delete admin_admin
        $data = array();
        $data['deleted_at'] = time();
        \DB::connection('mysql')->table('admin_admins')->where('id', $admin_admin->id)->update($data);

        // success
        $response = array();
        $response['error'] = 0;
        $response['message'] = 'Success';
        return $response;
    }
}
