<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Telegram;
use App\Models\RouterOs;

class TelegramController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->all();
        $usercheck = Telegram::where('telegram_id',$data['telegram_id'])->first();
        if (!$usercheck) {
            $user = Telegram::create($data);
            return response()->json($user,201);
        } else return response()->json($data,200);
        
    }

    public function verified(Request $request, $id)
    {
        $user = Telegram::where('id',$id)->first();
        $user->verified = $request->verified;
        $now = date("Y-m-d H:i:s");
        $user->verified_at = $now;
        $user->update();
        return reponse()->json($user,200);
    }

    public function getTelegramUser(Request $request, $telegram_id)
    {
        $telegramuser = Telegram::where('telegram_id', $telegram_id)->first();
        
        if ($telegramuser) {
            $data['exist'] = true;
            $data['user'] = $telegramuser;
            return response()->json($data,200);
        } else {
            $data['exist'] = false;
            $data['user'] = null;
            return response()->json($data,200);
        }
    }

    public function downloadMacBinding()
    {
        $ip = env('MIKROTIK_IP');
        $user = env('MIKROTIK_USER');
        $password = env('MIKROTIK_PASSWORD');
        $API = new RouterOs();
        $API->debug = false;

        if ($API->connect($ip, $user, $password)) {
            $macbinds = $API->comm('/ip/hotspot/ip-binding/print');
            $filename = "macbinding.csv";
            $fp = fopen($filename,"w+");
            fputcsv($fp, array('No','Mac Address','Address','To Address','Type','Bypassed','Disabled'));
            $i=1;
            foreach ($macbinds as $mac) {
                fputcsv($fp, array($i, $mac['mac-address'],$mac['address'],$mac['to-address'],$mac['type'],$mac['bypassed'],$mac['disabled']));
                $i = $i + 1;
            }
            fclose($fp);
            $headers = array('Content-Type' => 'text/csv');

            return response()->download($filename,'macbinding.csv', $headers);
        } else {
            return ;
        }
    }

    public function downloadUserActive()
    {
        $ip = env('MIKROTIK_IP');
        $user = env('MIKROTIK_USER');
        $password = env('MIKROTIK_PASSWORD');
        $API = new RouterOs();
        $API->debug = false;
        if ($API->connect($ip, $user, $password)) {
            $useractives = $API->comm('/ip/hotspot/active/print');
            $filename = "useractives.csv";
            $fp = fopen($filename,"w+");
            fputcsv($fp, array('No','Server','User','Address','Mac Address','Login by','Uptime','Session Time Left','Idle Time','Bytes In','Bytes Out','Packets In', 'Packets Out'));
            $i = 1;
            foreach ($useractives as $useractive) {
                fputcsv($fp,array($i,$useractive['server'],$useractive['user'],$useractive['address'],$useractive['mac-address'],$useractive['login-by'],$useractive['uptime'],$useractive['session-time-left'],$useractive['idle-time'],$useractive['bytes-in'],$useractive['bytes-out'],$useractive['packets-in'],$useractive['packets-out']));
                $i = $i + 1;
            }
            fclose($fp);
            $headers = array('Content-Type' => 'text/csv');
            return response()->download($filename,'useractives.csv', $headers);

        } else {
            return ;
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
        $data_mac = $request->all();
        if ($this->isValidMacAddress($data_mac['mac'])) {
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
                return response()->json($data,201);
            } else {
                $data['error'] = true;
                $data['msg'] = "Error connect to gateway!";
                return response()->json($data,200);
            }
        } else {
            $data['error'] = true;
            $data['msg'] = "Mac Address invalid! ".$request->mac;
            return response()->json($data,200);
        }

    }

    public function getUsers()
    {
        $users = Telegram::get();
        return response()->json($users,200);
    }
}
