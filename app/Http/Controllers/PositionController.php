<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    function update(Request $request) {
        $request->validate([
            'log' => 'required',
            'lat' => 'required'
        ]);

        $user = Auth::user();
        $user->lat = $request->lat;
        $user->log = $request->log;
        $user->last_location_at = date('Y-m-d H:i:s');
        $user->save();

        return response([
            'success' => true,
            'errors' => 'localização atualizada'
        ], 200);
    }
}
