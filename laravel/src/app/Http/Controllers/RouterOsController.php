<?php

namespace App\Http\Controllers;

use App\Models\RouterOs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class RouterOsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ip = env('MIKROTIK_IP');
        $user = env('MIKROTIK_USER');
        $password = env('MIKROTIK_PASSWORD');
        $API = new RouterOs();
        $API->debug = false;
        $data = [];
        if ($API->connect($ip, $user, $password)) {

                        $system = $API->comm('/system/resource/print');

            $data = [
                'error' => false,
                'title' => 'System Resource',
                'system' => $system,
            ];
            //dd($data);
            // return view('admin.dashboardv2',compact('data'));
            //dd($data);
            return response()->json($data);

                } else {
                    $data = [
                        'error' => true,
                        'title' => 'System Resource',
                        'msg' => 'Error connect to mikrotik',
                    ];

                    // return view('admin.dashboardv2',compact('data'));
                    return respone()->json($data);
            }
    }


    public function showMacBind()
    {
        $ip = env('MIKROTIK_IP');
        $user = env('MIKROTIK_USER');
        $password = env('MIKROTIK_PASSWORD');
        $API = new RouterOs();
        $API->debug = false;
        $data = [];
        if ($API->connect($ip, $user, $password)) {

                        $system = $API->comm('/ip/hotspot/ip-binding/print');

            $data = [
                'error' => false,
                'title' => 'Mac Add Binding',
                'macbind' => $system,
            ];
            //dd($data);
            // return view('admin.dashboardv2',compact('data'));
            //dd($data);
            return response()->json($data);

                } else {
                    $data = [
                        'error' => true,
                        'title' => 'Mac Add Binding',
                        'msg' => 'Error connect to mikrotik',
                    ];

                    // return view('admin.dashboardv2',compact('data'));
                    return response()->json($data);
            }
    }

    function isValidMacAddress($macAddress) {
        // Pola regex untuk MAC address yang valid
        $pattern = '/^([0-9A-Fa-f]{2}[:]){5}([0-9A-Fa-f]{2})$/';

        // Validasi menggunakan preg_match
        if (preg_match($pattern, $macAddress)) {
            return true;
        } else {
            return false;
        }
    }

    public function addMaccBinding(Request $request)
    {
        $data_mac = $request->mac;
        if ($this->isValidMacAddress($data_mac)) {
            $ip = env('MIKROTIK_IP');
            $user = env('MIKROTIK_USER');
            $password = env('MIKROTIK_PASSWORD');
            $API = new RouterOs();
            $API->debug = false;

            if ($API->connect($ip, $user, $password)) {
                $API->comm('/ip/hotspot/ip-binding/add',[
                    'mac-address'=> $request->mac,
                    'type' => $request->type,
                    'comment' => $request->comment,
                ]);
                $data['error'] = false;
                $data['msg'] = "Mac Address has been added";
                // return response()->json($data,201);
                return redirect()->route('mac');
            } else {
                $data['error'] = true;
                $data['msg'] = "Error connect to gateway!";
                // return response()->json($data,200);
                return redirect()->route('mac')->with(['messages'=>$data['msg']]);
            }
        }  else {
            $data['error'] = true;
            $data['msg'] = "Mac Address invalid! ".$request->mac;
            return redirect()->route('mac')->with(['messages'=>$data['msg']]);
            // return response()->json($data,200);
        }
    }

    public function getMacAdd($id)
    {
        $ip = env('MIKROTIK_IP');
        $user = env('MIKROTIK_USER');
        $password = env('MIKROTIK_PASSWORD');
        $API = new RouterOs();
        $API->debug = false;

        if ($API->connect($ip, $user, $password)) {
            $getmac = $API->comm('/ip/hotspot/ip-binding/print', [
                                "?.id" => $id,
                        ]);
            $data['error'] = false;
            $data['mac'] = $getmac;
            return response()->json($data,200);
        } else {
            $data['error'] = true;
            $data['msg'] = "Error connection to the gateway!";
            return response()->json($data,200);
        }
    }

    public function updateMacBinding(Request $request, $id)
    {
        $ip = env('MIKROTIK_IP');
        $user = env('MIKROTIK_USER');
        $password = env('MIKROTIK_PASSWORD');
        $API = new RouterOs();
        $API->debug = false;

        if ($this->isValidMacAddress($request->mac)) {
            $validator = Validator::make($request->all(), [
                'mac' => ['required'],
                'type' => ['required'],
                'comment' => ['required'],
                'disabled' => ['required'],
            ]);
            if($validator->fails()){
                $data['error'] = true;
                $data['msg'] = $validator->messages();
                return response()->json($data, 200);
            }
            if ($API->connect($ip, $user, $password)) {
                $API->comm("/ip/hotspot/ip-binding/set",[
                    '.id' => $id,
                    'mac-address' => $request->mac,
                    'type' => $request->type,
                    'comment' => $request->comment,
                    'disabled' => $request->disabled
                ]);
                $data['error'] = false;
                $data['msg'] = "Mac Address updated!";
                return redirect()->route('mac');
                // return response()->json($data,200);
            }
        } else {
            $data['error'] = true;
            $data['msg'] = "Mac Address invalid! ".$request->mac;
            return redirect()->route('mac')->with(['messages'=>$data['msg']]);
            // return response()->json($data,200);
        }
    }

    public function showUserProfile()
    {
        $ip = env('MIKROTIK_IP');
        $user = env('MIKROTIK_USER');
        $password = env('MIKROTIK_PASSWORD');
        $API = new RouterOs();
        $API->debug = false;
        $data = [];
        if ($API->connect($ip, $user, $password)) {


            $system = $API->comm('/ip/hotspot/user/profile/print');

            $data = [
                'error' => false,
                'title' => 'User Hotspot Profile',
                'userprofile' => $system,
            ];
            //dd($data);
            // return view('admin.dashboardv2',compact('data'));
            //dd($data);
            return response()->json($data);

                } else {
                    $data = [
                        'error' => true,
                        'title' => 'User Hotspot Profile',
                        'msg' => 'Error connect to mikrotik',
                    ];

                    // return view('admin.dashboardv2',compact('data'));
                    return response()->json($data);
        } 
    }

    public function showActiveUser()
    {
        $ip = env('MIKROTIK_IP');
        $user = env('MIKROTIK_USER');
        $password = env('MIKROTIK_PASSWORD');
        $API = new RouterOs();
        $API->debug = false;
        $data = [];
        if ($API->connect($ip, $user, $password)) {

                        $system = $API->comm('/ip/hotspot/active/print');

            $data = [
                'error' => false,
                'title' => 'Active User',
                'activeuser' => $system,
            ];
            // return view('routeros.activeuser',compact('data'));
            return response()->json($data);

                } else {
                    $data = [
                        'error' => true,
                        'title' => 'User Hotspot Profile',
                        'msg' => 'Error connect to mikrotik',
                    ];
                    // return view('routeros.activeuser',compact('data'));
                    return response()->json($data);
        }
    }
    

    public function deleteMacBinding(Request $request)
    {
        $ip = env('MIKROTIK_IP');
        $user = env('MIKROTIK_USER');
        $password = env('MIKROTIK_PASSWORD');
        $API = new RouterOs();
        $API->debug = false;

        if ($API->connect($ip, $user, $password)) {
            $API->comm("/ip/hotspot/ip-binding/remove",[
                '.id' => $request->id
            ],);
            $data['error'] = false;
            $data['msg'] = "Mac Address has been deleted!";
            // return response()->json($data,200);
            return redirect()->route('mac');
        } else {
            $data['error'] = true;
            $data['msg'] = "Error connected to gateway!";
            // return response()->json($data,200);
            return redirect()->route('mac')->with(['messages'=>$data['msg']]);
        }
    }

    public function getUserProfiles()
    {
        $ip = env('MIKROTIK_IP');
        $user = env('MIKROTIK_USER');
        $password = env('MIKROTIK_PASSWORD');
        $API = new RouterOs();
        $API->debug = false;
        $data = [];
        if ($API->connect($ip, $user, $password)) {
            $system = $API->comm('/ip/hotspot/profile/print');
            $data = [
                'error' => false,
                'title' => 'Hotspot User Profiles',
                'userprofile' => $system,
            ];
            // return view('routeros.activeuser',compact('data'));
            return response()->json($data,200);

        } else {
                    $data = [
                        'error' => true,
                        'title' => 'User Profile',
                        'msg' => 'Error connect to mikrotik',
                    ];
                    // return view('routeros.activeuser',compact('data'));
                    return response()->json($data,200);
        }
    }

    public function addUserProfile(Request $request)
    {
        $datareq = $request->all();
        $data = [];
        $validator = Validator::make($datareq, [
            'name' => ['required']
        ]);
        $session_timeout = ($datareq['session-timeout'] !== null) ? $datareq['session-timeout'] : "00:00:00";
        $rate_limit = ($datareq['rate-limit'] !== null) ? $datareq['rate-limit'] : "";
        $shared_users = ($datareq['shared-users'] !== null) ? $datareq['shared-users'] : "1";
        if ($validator)
        {
            if ($validator->fails())
            {
                $data['error'] = true;
                $data['exist'] = false;
                $data['msg'] = $validator->messages();
                return response()->json($data,200);
            } else {
                $ip = env('MIKROTIK_IP');
                $user = env('MIKROTIK_USER');
                $password = env('MIKROTIK_PASSWORD');
                $API = new RouterOs();
                $API->debug = false;
                if ($API->connect($ip, $user, $password)) {
                    $API->comm('/ip/hotspot/user/profile/add',[
                        'name'=> $datareq['name'],
                        'session-timeout' => $session_timeout,
                        'rate-limit' => $rate_limit,
                        'shared-users' => $shared_users
                    ]);
                    $data['error'] = false;
                    $data['msg'] = "User profile has been added";
                    return response()->json($data,200);
                } else {
                    $data['error'] = true;
                    $data['msg'] = "Error connect to gateway!";
                    return response()->json($data,200);
                }

            }
        }
    }

    public function updateUserProfile(Request $request, $id)
    {
        $datareq = $request->all();
        $data = [];
        $validator = Validator::make($datareq, [
            'name' => ['required']
        ]);
        $session_timeout = ($datareq['session-timeout'] !== null) ? $datareq['session-timeout'] : "00:00:00";
        $rate_limit = ($datareq['rate-limit'] !== null) ? $datareq['rate-limit'] : "";
        $shared_users = ($datareq['shared-users'] !== null) ? $datareq['shared-users'] : "1";
        if ($validator)
        {
            if ($validator->fails())
            {
                $data['error'] = true;
                $data['exist'] = false;
                $data['msg'] = $validator->messages();
                return response()->json($data,200);
            } else {
                $ip = env('MIKROTIK_IP');
                $user = env('MIKROTIK_USER');
                $password = env('MIKROTIK_PASSWORD');
                $API = new RouterOs();
                $API->debug = false;
                if ($API->connect($ip, $user, $password)) {
                    $API->comm('/ip/hotspot/user/profile/set',[
                        '.id' => $id,
                        'name'=> $datareq['name'],
                        'session-timeout' => $session_timeout,
                        'rate-limit' => $rate_limit,
                        'shared-users' => $shared_users
                    ]);
                    $data['error'] = false;
                    $data['msg'] = "User profile has been updated";
                    return response()->json($data,200);
                } else {
                    $data['error'] = true;
                    $data['msg'] = "Error connect to gateway!";
                    return response()->json($data,200);
                }

            }
        }
    }

    public function getUserProfile($id)
    {
        $ip = env('MIKROTIK_IP');
        $user = env('MIKROTIK_USER');
        $password = env('MIKROTIK_PASSWORD');
        $API = new RouterOs();
        $API->debug = false;

        if ($API->connect($ip, $user, $password)) {
            $getprofile = $API->comm('/ip/hotspot/user/profile/print', [
                                "?.id" => $id,
                        ]);
            $data['error'] = false;
            $data['userprofile'] = $getprofile;
            return response()->json($data,200);
        } else {
            $data['error'] = true;
            $data['msg'] = "Error connection to the gateway!";
            return response()->json($data,200);
        }
    }

    public function deleteUserprofile(Request $request)
    {
        $ip = env('MIKROTIK_IP');
        $user = env('MIKROTIK_USER');
        $password = env('MIKROTIK_PASSWORD');
        $API = new RouterOs();
        $API->debug = false;

        if ($API->connect($ip, $user, $password)) {
            $API->comm("/ip/hotspot/user/profile/remove",[
                '.id' => $request->id
            ],);
            $data['error'] = false;
            $data['msg'] = "User profile` has been deleted!";
            // return response()->json($data,200);
            return redirect()->route('user.profile');
        } else {
            $data['error'] = true;
            $data['msg'] = "Error connected to gateway!";
            // return response()->json($data,200);
            return redirect()->route('user.profile')->with(['messages'=>$data['msg']]);
        }
    }
    

    public function fetchHtml($traffic)
    {
        if ($traffic == 'wan') {
            $url = 'http://222.165.249.230/graphs/iface/ether1/'; // Ganti dengan URL target kamu
        } else if ($traffic == 'guest') {
            $url = 'http://222.165.249.230/graphs/iface/VLAN%2D50/';
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // hasil sebagai string
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $html = curl_exec($ch);

        if (curl_errno($ch)) {
            abort(500, 'Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        // Langsung kembalikan sebagai konten HTML
        return response($html, 200)
               ->header('Content-Type', 'text/html');
    }

    public function wanTraffic()
    {
        $data['traffic'] = 'wan';
        return view('routeros.traffic',$data);
    }
    
    public function guestTraffic()
    {
        $data['traffic'] = 'guest';
        return view('routeros.traffic',$data);
    }
}
