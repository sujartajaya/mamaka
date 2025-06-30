<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private $perpage = 10;
    private $totalrows = 0;

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email:rfc,dns','unique:users'],
            'username' => ['required', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'same:confirm_password'],
            'confirm_password' => ['min:8']
        ]);
        $credentials['password'] = bcrypt($credentials['password']);
        $user = User::create($credentials);
        if ($user)
        {
            return redirect( route('login') );
        }
    }

    public function authtenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // return redirect()->back();
            // return redirect()->intended(route('home'));
            if ($request->prev_url != url()->current()){
                return redirect()->intended($request->prev_url);
            } else return redirect()->intended( route('home'));
        }

         return back()->with('loginError','Login Failed!');
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect( route('home') );
    }

    public function getUsers(Request $request)
    {
        $page = 1;

        if ($request->page) {
            $page = $request->page;
        }


        $offset = $this->perpage * $page - $this->perpage;
        // $offset = 1;
        $data['rows'] = User::search($request->search)->get()->count();
        $this->totalrows = $data['rows'];

        $qr = "select * from users where `name` like '%".$request->search."%' or `email` like '%".$request->search."%' or `username` like '%".$request->search."%' or `type` like '%".$request->search."%' limit ".$offset.",".$this->perpage;

        // $users = User::search($request->search)->paginate($perpage);
        $users = DB::select($qr);

        //$rows = count($users);

        $data['users'] = $users;
        $data['perpage'] = $this->perpage;

        // $data['qr'] = $qr;

        return response()->json($data,200);
    }

    public function userlists(Request $request)
    {

        $data['perpage'] = $this->perpage;
        $data['totalrows'] = User::search($request->search)->get()->count();

        return view('user.users',compact('data'));
    }
    
    public function save(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email:rfc,dns','unique:users'],
            'username' => ['required', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'same:confirm_password'],
            'confirm_password' => ['min:8']
        ]);
        $credentials['password'] = bcrypt($credentials['password']);
        $user = User::create($credentials);
        if ($user) {
            return response()->json($user,201);
        }
    }

    public function allUsers(Request $request)
    {
        $users = User::get();
        $data = [];
        if ($users) {
            $data['error'] = false;
            $data['users'] = $users;
        } else {
           $data['error'] = true;
            $data['message'] = "Error: can not get users data!";
        }
        return response()->json($data,200);
    }

    /**API for javascript */
    public function storeNewUser(Request $request)
    {
        $datareq = $request->all();
        $data = [];

        $validator = Validator::make($datareq, [
            'name' => ['required'],
            'email' => ['required', 'email:rfc,dns','unique:users'],
            'username' => ['required', 'unique:users'],
            'type' => ['required'],
            'password' => ['required', 'string', 'min:8', 'same:confirm_password'],
            'confirm_password' => ['min:8']
        ]);
        if ($validator) {
            if ($validator->fails()) {
                $data['error'] = true;
                $data['msg'] = $validator->messages();
                return response()->json($data,200);
            } else {
                $datareq['password'] = bcrypt($datareq['password']);
                $data['error'] = false;
                $user = User::create($datareq);
                $data['msg'] = $user;
                return response()->json($data,201);
            }
        
        } else {
            $data['error'] = true;
            $data['msg'] = $validator->messages();
            return response()->json($data,200);
        }
    }

    /** API for get user by id for js */
    public function getUserById($id)
    {
        $user = User::where('id',$id)->first();
        return response()->json($user,200);
    }

    /** API for update user for js */
    public function userUpdate(Request $request, $id)
    {
        $datareq = $request->all();
        $data = [];
        $user = User::where('id',$id)->first();
        $validator = Validator::make($datareq, [
            'name' => 'required',
            'email' => 'required|email:rfc,dns|unique:users,email'.$user->id,
            'username' => 'required|unique:users,username'.$user->id,
            'type' => ['required'],
        ]);
        if ($validator) {
            if ($validator->fails()) {
                $data['error'] = true;
                $data['msg'] = $validator->messages();
                return response()->json($data,200);
            } else {
                $data['error'] = false;
                $dataupdate['name'] = $datareq['name'];
                $dataupdate['email'] = $datareq['email'];
                $dataupdate['username'] = $datareq['username'];
                $dataupdate['type'] = $datareq['type'];
                $user->update($dataupdate);
                $data['msg'] = 'User updated!';
                return response()->json($data,200);
            }
        } else {
            $data['error'] = true;
            $data['msg'] = $validator->messages();
            return response()->json($data,200);
        }
    }
}
