<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SmsController extends Controller
{
    function invite(Request $request) {
        $request->validate([
            'number' => 'required'
        ]);

        $user = Auth::user();
        
        $request->number = preg_replace('/\D/', '', $request->number);
        if (substr($request->number, 0, 2) != '55') {
            $request->number = '55' . $request->number;
        }

        $response = Http::post('https://api.mobizon.com.br/service/Message/SendSmsMessage?apiKey=br9490849006e3cf2e7c718c37a4c68c580710de847f26762e5e4bc6170b127badf7ca', [
            'recipient' => $request->number,
            'text' => "Examplo de convite SOS APP"
        ]);


        if ($response->successful()) {
            return response([
                "success" => true,
                "message" => 'Convite enviado com sucesso!!'
            ], 200);
        } else {
            return response([
                "success" => false,
                "message" => 'Error ao enviar convite!!'
            ], 400);
        }
    }
}
