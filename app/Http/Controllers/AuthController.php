<?php

namespace App\Http\Controllers;

use App\Mail\Recover;
use App\Models\User;
use App\Models\EmailCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    function register(Request $request) {
        $request->validate([
            'phone' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ],
        [
            'phone.unique' => 'Numero de telefone já registrado',
            'email.unique' => 'email já registrado'
        ]);

        $user = new User;
        $user->email = request('email');
        $user->phone = request('phone');
        $user->password = bcrypt(request('password'));
        $user->save();

        return response([
            "success" => true,
            "user" => $user
        ], 200);
    }

    function signin(Request $request) {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', request('email'))->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                "success" => false,
                "errors" => 'Login ou senha invalidos'
            ], 400);
        }

        return response([
            "success" => true,
            "token" => $user->createToken($user->email)->plainTextToken,
            "user" => $user
        ], 200);
    }

    function check() {
        return response([
            "success" => true,
            "user" => Auth::user()
        ], 200);
    }

    function sendEmailRecover(Request $request) {
        $request->validate([
            'email' => 'required|email'
        ],
        [
            'email.required' => 'Email informado é invalido'
        ]);
        

        if (User::where('email', $request->email)->get()->count() == 0) {
            return response([
                "success" => false,
                "errors" => 'Email invalido'
            ], 400);
        }
        $user = User::where('email', $request->email)->first();
        
        $FourDigitRandomNumber = rand(1231,7879);
        $emailCode = new EmailCode;
        $emailCode->user_id = $user->id;
        $emailCode->code = $FourDigitRandomNumber;
        $emailCode->save();

        Mail::to($user->email)->send(new Recover($FourDigitRandomNumber));


        return response([
            "success" => true,
            "message" => 'Codigo enviado para o email'
        ], 200);
    }

    function checkCode(Request $request) {
        $request->validate([
            'code' => 'required|numeric'
        ]);

        if (EmailCode::where('code', $request->code)->where('status', 0)->get()->count() > 0) {
            return response([
                "success" => true,
                "message" => 'Codigo valido'
            ], 200);
        } else {
            return response([
                "success" => false,
                "message" => 'Codigo invalido'
            ], 400);
        }
    }

    function changePassword(Request $request) {
        $request->validate([
            'code' => 'required|numeric',
            'password' => 'required'
        ]);

        if (EmailCode::where('code', $request->code)->where('status', 0)->get()->count() > 0) {
            $code = EmailCode::where('code', $request->code)->where('status', 0)->first();
            $code->status = 1;
            $code->save();

            $user = User::find($code->user_id);
            $user->password = bcrypt(request('password'));
            $user->save();


            return response([
                "success" => true,
                "message" => 'Senha alterada com sucesso'
            ], 200);
        } else {
            return response([
                "success" => false,
                "message" => 'Codigo invalido'
            ], 400);
        }
    }
}
