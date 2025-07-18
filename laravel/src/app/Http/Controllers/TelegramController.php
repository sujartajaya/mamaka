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
        $user = Telegram::create($data);
        return response()->json($user,201);
    }

    public function verified(Request $request, $id)
    {
        $user = Telegram::where('id',$id)->first();
        $user->verified = $request->verified;
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
}
