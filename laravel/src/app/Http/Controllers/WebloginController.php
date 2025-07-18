<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guest;
use App\Models\Device;
use App\Models\Radcheck;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class WebloginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $data = $request->all();

        $mac_add = $data['mac'];
       
        // $guest = Guest::where('mac_add',$mac_add)->first();
        /** carinya di devices untuk mac-add guest karena ada perubahan*/
        $device = Device::where('mac_add',$mac_add)->first();

        // if ($guest) {
        if ($device) {
            // $guest->os_client = $this->getOS();
            // $guest->browser_client = $this->getBrowser();
            $guest = Guest::where('id',$device->guest_id)->first();
            $data_update = [
                "os_client" =>  $this->getOS(),
                "browser_client" => $this->getBrowser()
            ];
            $device->update($data_update);
            // $guest->save();
            return view('weblogin.loginv2',compact('data','guest'));

        } else {
            return view('weblogin.loginv2',compact('data'));
        }
        
        // return view('weblogin.loginv2',compact('data','guest'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datareq = $request->all();
        $data = [];
        /** bisa dibalikan */
        // $datareq['os_client'] = $this->getOS();
        // $datareq['browser_client'] = $this->getBrowser();

        $guest = Guest::where('email',$datareq['email'])->first();
        if ($guest) {
            $data['error'] = false;
            $data['exist'] = true;
            $data['msg'] = $guest;

            /**  apakah device apakah sudah pernah terdaftar */
            $device = Device::where('mac_add',$datareq['mac_add'])->first();

            if ($device) {
                // $device->mac_add = $datareq['mac_add'];
                $device->os_client = $this->getOS();
                $device->browser_client = $this->getBrowser();
                $davice->update();
            } else {
                $datadevice['guest_id'] = $guest->id;
                $datadevice['mac_add'] = $datareq['mac_add'];
                $datadevice['os_client'] = $this->getOS();
                $datadevice['browser_client'] = $this->getBrowser();
                $device = Device::create($datadevice);
            }
            /** ini awal bisa dibalikin */
            // $guest->mac_add = $datareq['mac_add'];
            // $guest->os_client = $this->getOS();
            // $guest->browser_client = $this->getBrowser();
            // $guest->save();
            
            return response()->json($data,200);
        }

        $datareq['username'] = Str::random(10);
        $datareq['password'] = Str::password(8);

        $dataguest['name'] = $datareq['name'];
        $dataguest['email'] = $datareq['email'];
        $dataguest['username'] = $datareq['username'];
        $dataguest['password'] = $datareq['password'];
        $dataguest['country_id'] = $datareq['country_id'];
        
        $datadevice['mac_add'] = $datareq['mac_add'];
        $datadevice['os_client'] = $this->getOS();
        $datadevice['browser_client'] = $this->getBrowser();

        
        $validator = Validator::make($datareq, [
                'name' => ['required'],
                'email' => ['required','email:rfc,dns','unique:guests'],
                'username' => ['required','unique:guests'],
                'password' => ['required'],
                'mac_add' => ['required'],
                'country_id' => ['required']
        ]);
        
        if ($validator) 
        {
            if ($validator->fails())
            {
                $data['error'] = true;
                $data['exist'] = false;
                $data['msg'] = $validator->messages();
                return response()->json($data,200);
            } else {
                $data['error'] = false;
                $data['exist'] = false;

                // $guest = Guest::create($datareq);
                // $guest = Guest::create($dataguest);
                $guest = new Guest();
                $guest->name = $datareq['name'];
                $guest->email = $datareq['email'];
                $guest->country_id = $datareq['country_id'];
                $guest->username = $datareq['username'];
                $guest->password = $datareq['password'];
                $guest->save();
               
                $guest = Guest::where('email',$datareq['email'])->first();
                
                $datadevice['guest_id'] = $guest->id;

                $userlogin['username'] = $datareq['username'];
                $userlogin['op'] = ":=";
                $userlogin['attribute'] = "Cleartext-Password";
                $userlogin['value'] = $datareq['password'];

                $radcheck = Radcheck::create($userlogin);
                
                $device = Device::create($datadevice);

                $data['msg'] = $guest;

                // $userlogin['username'] = $datareq['username'];
                // $userlogin['op'] = ":=";
                // $userlogin['attribute'] = "Cleartext-Password";
                // $userlogin['value'] = $datareq['password'];

                // $radcheck = Radcheck::create($userlogin);

                return response()->json($data,201);
            }
        }
    }

    private function getOS() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        $os_array = [
            '/windows nt 10/i'     => 'Windows 10',
            '/windows nt 6.3/i'     => 'Windows 8.1',
            '/windows nt 6.2/i'     => 'Windows 8',
            '/windows nt 6.1/i'     => 'Windows 7',
            '/windows nt 6.0/i'     => 'Windows Vista',
            '/windows nt 5.1/i'     => 'Windows XP',
            '/macintosh|mac os x/i' => 'Mac OS',
            '/linux/i'              => 'Linux',
            '/ubuntu/i'             => 'Ubuntu',
            '/iphone/i'             => 'iPhone',
            '/ipad/i'               => 'iPad',
            '/android/i'            => 'Android',
        ];
        
        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                return $value;
            }
        }
        return 'Unknown OS';
    }

    private function getBrowser() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        $browser_array = [
            '/msie/i'      => 'Internet Explorer',
            '/firefox/i'   => 'Firefox',
            '/safari/i'    => 'Safari',
            '/chrome/i'    => 'Chrome',
            '/edge/i'      => 'Edge',
            '/opera/i'     => 'Opera',
            '/netscape/i'  => 'Netscape',
            '/maxthon/i'   => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
        ];
        
        foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                return $value;
            }
        }
        return 'Unknown Browser';
    }

    public function getGuests(Request $request)
    {

        if ($request->has('startdate')) {
            $startdate = $request->startdate;
        } else $startdate = Carbon::now()->firstOfMonth();
        if ($request->has('enddate')) {
            $enddate = Carbon::createFromFormat('Y-m-d', $request->enddate);
            $enddate->addDays(1);
        } else $enddate = now();
        

        $sqr = "select guests.id as no, guests.name as name, guests.email as email, guests.username as username, guests.created_at as created_at, guests.updated_at as updated_at, sum(radacct.acctinputoctets) as byteinput,sum(radacct.acctoutputoctets) as byteoutput, countries.country_name from guests, radacct, countries where guests.username=radacct.username and guests.country_id = countries.id and radacct.acctstarttime >= '".$startdate."' and radacct.acctstarttime < '".$enddate."' group by radacct.username order by guests.created_at asc";

        $guests = DB::select($sqr);

        $count = sizeof($guests);
        
        $data['guests'] = $guests;
        
        $data['rows'] = $count;

        $data['sqr'] = $sqr;

        $data['startdate'] = $request->startdate;

        $data['enddate'] = $request->enddate;

        return view('guest.guests',compact('data'));

        // return response()->json($data,200);
    }

}
