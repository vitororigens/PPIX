<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    function changePasswords(Request $request) {
        $request->validate([
            'passwordBank' => 'nullable',
            'passwordApp' => 'nullable',
            'passwordEmergecy' => 'nullable'
        ]);

        $user = Auth::user();
        if ($request->passwordBank) {
            $user->passwordBank = $request->passwordBank;
        } else {
            $user->passwordBank = '';
        }
        if ($request->passwordApp) {
            $user->passwordApp = $request->passwordApp;
        } else {
            $user->passwordApp = '';
        }   
        if ($request->passwordEmergecy) {
            $user->passwordEmergecy = $request->passwordEmergecy;
        } else {
            $user->passwordEmergecy = '';
        }
        $user->save();

        return response([
            "success" => true,
            "message" => 'Senhas alteradas com sucesso'
        ], 200);
    }

    function changeCar(Request $request){
        $request->validate([
            'car_id' => 'required'
        ]);
        $user = Auth::user();
        if (Car::where('id', $request->car_id)->where('user_id', $user->id)->get()->count() > 0) {
            $user->car_id = $request->car_id;
            $user->save();
            return response([
                "success" => true,
                "message" => 'Carro atualizado com sucesso'
            ], 200);
        } else {
            return response([
                "success" => false,
                "message" => 'Ocorreu um error'
            ], 400);
        }
    }
}
