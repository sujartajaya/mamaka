<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use DB;

class GuestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $os = $this->getOS();
        $browser = $this->getBrowser();

        echo "Sistem Operasi: $os <br>";
        echo "Browser: $browser";
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



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $mac)
    {
        $guest = Guest::where('mac_add',$mac)->first();
        return response()->json($guest,200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guest $guest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guest $guest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guest $guest)
    {
        //
    }

    public function displaydata(Request $request)
    {
        $page = $request->query('page');
        $perpage = 3;
        if(!isset($page)) { $page = 1;}
        $totaldata = Guest::get()->count();
        $totalpage = ceil($totaldata / $perpage);

        $offset = $page * $perpage - $perpage;

        $qr = 'select guests.*, sum(radacct.acctinputoctets) as jumlah, countries.country_name from guests, radacct, countries where guests.username=radacct.username and guests.country_id = countries.id group by radacct.username order by guests.created_at asc';
        $qr = $qr.' limit '.$perpage.' offset '.$offset;

        $guest = DB::select($qr);

        return response()->json($guest,200);
    }
}
