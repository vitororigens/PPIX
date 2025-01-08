<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AlertController extends Controller
{
    // function getAccessToken()
    // {
    //     return 'ola';
    //     $apiKey = 'SUA_CHAVE_API_DO_FIREBASE';
    //     $customToken = 'TOKEN_GERADO_DO_FIREBASE';

    //     $data = [
    //         'token' => $customToken,
    //         'returnSecureToken' => true,
    //     ];

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, 'https://identitytoolkit.googleapis.com/v1/accounts:signInWithCustomToken?key=' . $apiKey);
    //     curl_setopt($ch, CURLOPT_POST, 1);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //     $response = curl_exec($ch);
    //     curl_close($ch);

    //     $result = json_decode($response, true);

    //     if (isset($result['idToken'])) {
    //         $fcmToken = $result['idToken'];
    //         echo "Token FCM: " . $fcmToken;
    //     } else {
    //         echo "Erro ao obter token FCM.";
    //     }
    // }

    function index()
    {
        $alerts = Alert::select('email', 'finished_lat', 'finished_log', 'alerts.id', 'status', 'sender_id', 'alerts.car_id', 'isCleaned', 'alerts.id')->with('car')->where('user_id', Auth::id())->where('isCleaned', false)->orwhere('sender_id', Auth::id())->where('isCleaned', false)->join('users', 'users.id', 'alerts.sender_id')->orderBy('id', 'desc')->get();
        foreach ($alerts as $alert) {
        }
        return response([
            "success" => true,
            "alerts" => $alerts
        ], 200);
    }

    function wait()
    {
        $alerts = Alert::select('email', 'lat', 'log', 'alerts.id', 'alerts.car_id')->with('car')->where('user_id', Auth::id())->where('status', 0)->where('stopAlerta', false)->join('users', 'users.id', 'alerts.sender_id')->get();

        foreach ($alerts as $alert) {
        }
        return response([
            "success" => true,
            "alerts" => $alerts
        ], 200);
    }

    function create(Request $request)
    {
        $user = Auth::user();
        $alerts = Group::where('leader_id', $user->id)->get();

        if ($alerts->isempty()) {
            return response('Alerts Vazio!', 401);
        }

        $apiURL = "https://fcm.googleapis.com/fcm/send";

        $accessToken = 'AAAAApapSEk:APA91bHPX2T9PrAhFwIHzeo0k8TToEfEemMvKqy_zCO9RoAu6_Kmr1UJZuqIlZBM59x2Itas4ezIsjgg3R2mwxodEjXw0o5H1DASX6AZr5a0iPxblJytYoxiVr8L5bGRLEnnGBYAu0aR';

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'key=' . $accessToken
        ];

        foreach ($alerts as $group) {
            if ($group->user->fcm_token != '') {

                // $message = [
                //     'to' => $group->user->fcm_token,
                //     'notification' => [
                //         'title' => $group->user->email,
                //         'body' => 'ok'
                //     ]
                // ];

                $message = [
                    "delay_while_idle" => false,
                    "android" =>  [
                        "priority" => "high"
                    ],
                    "to" => $group->user->fcm_token,
                    "data" => [
                        "email" => $group->user->email
                    ],
                    "priority" => 10
                ];
                $response = Http::withHeaders($headers)->post($apiURL, $message);
            }


            $alert = new Alert();
            $alert->sender_id = $user->id;
            $alert->user_id = $group->user_id;
            $alert->car_id = $user->car_id;
            $alert->save();
        }

        try {
            // return response($response, 401);
            if ($response->status() == 200) {
                return response([
                    "success" => true,
                    "message" => 'Alerta enviado com sucesso'
                ], 200);
            } else {
                return response($response, 401);
            }
            // return response($response,401);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function stopAlert(Request $request)
    {
        $request->validate([
            'alert_id' => 'required'
        ]);

        $user = Auth::user();
        if (Alert::where('id', $request->alert_id)->where('user_id', $user->id)->get()->count() == 0) {
            return response([
                "success" => false,
                "errors" => 'Ocorreu um erro na finalização do alerta'
            ], 500);
        }

        $alert = Alert::where('id', $request->alert_id)->first();

        $alert->stopAlerta = true;
        $alert->save();

        return response([
            "success" => true,
            "message" => 'Alerta finalizado com sucesso'
        ], 200);
    }

    function finish(Request $request)
    {
        $request->validate([
            'alert_id' => 'required'
        ]);

        $user = Auth::user();
        if (Alert::where('id', $request->alert_id)->where('user_id', $user->id)->get()->count() == 0) {
            return response([
                "success" => false,
                "errors" => 'Ocorreu um erro na finalização do alerta'
            ], 500);
        }

        $alert = Alert::where('id', $request->alert_id)->first();

        $alert->status = 1;
        $alert->finished_lat = $alert->sender->lat;
        $alert->finished_log = $alert->sender->log;
        $alert->save();

        return response([
            "success" => true,
            "message" => 'Alerta finalizado com sucesso'
        ], 200);
    }

    function finishAllSends(Request $request)
    {
        $user = Auth::user();

        Alert::where('user_id', $user->id)->delete();

        return response([
            "success" => true,
            "message" => 'Alerta finalizado com sucesso'
        ], 200);
    }

    function finishAll(Request $request)
    {
        $user = Auth::user();

        Alert::where('user_id', $user->id)->update([
            'isCleaned' => true
        ]);

        return response([
            "success" => true,
            "message" => 'Alerta finalizado com sucesso'
        ], 200);
    }

    function updateFcmToken(Request $request)
    {
        $request->validate([
            'fcmToken' => 'required'
        ]);

        $user = Auth::user();
        $user->fcm_token = $request->fcmToken;
        $user->save();

        return response([
            "success" => true,
            "message" => 'Token atualizado com sucesso'
        ], 200);
    }
}
