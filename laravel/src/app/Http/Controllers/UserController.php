<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use DB;

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
}
