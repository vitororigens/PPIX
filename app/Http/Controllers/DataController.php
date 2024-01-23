<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataController extends Controller
{
    function home() {
        $user_id = Auth::id();
        return response([
            "success" => true,
            "data" => [
                "groupNumbers" => Group::where('user_id', $user_id)->get()->count(),
                "groupContacts" => Group::where('leader_id', $user_id)->get()->count(),
                "carNumbers" => Car::where('user_id', $user_id)->where('isDeleted', false)->get()->count(),
            ]
        ], 200);
    }
}
