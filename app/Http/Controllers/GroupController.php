<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    function index() {
        $user = Auth::user();
        $groups =  Group::select('users.email', 'users.id', 'leader_id', 'phone', 'users.group_name')->where('user_id', Auth::id())->join('users', 'users.id', 'groups.leader_id')->get()->all();
        array_unshift($groups, (object)[
            'group_name' => $user->group_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'id' => $user->id,
            'leader_id' => $user->id,
         ]);
        foreach ($groups as $group) {
            $group->users = Group::select('email', 'phone')->where('leader_id', $group->leader_id)->join('users', 'users.id', 'groups.user_id')->get();
        }

        return response([
            "success" => true,
            "groups" => $groups
        ], 200);
    }

    function update(Request $request) {
        $request->validate([
            'name' => 'required',
        ]);
        
        $user = Auth::user();
        $user->group_name = $request->name;
        $user->save();

        return response([
            "success" => true,
            "message" => 'Nome alterado com sucesso'
        ], 200);
    }

    function validUsers(){
        return response([
            "success" => true,
            "numbers" => User::select('phone')->where('id', '!=', Auth::id())->get(),
            "group" => Group::select('phone')->where('leader_id', Auth::id())->join('users', 'users.id', 'groups.user_id')->get()
        ], 200);
        
    }

    function remove(Request $request) {
        $request->validate([
            'phone' => 'required'
        ]);

        $phone = (substr($request->phone, 0, 1) != '+') ? $request->phone : substr($request->phone, 3);
        $phone = preg_replace('/\D/', '', $phone);
        $leader_id = Auth::id();
        if (User::where('phone', $phone)->where('id', '!=', $leader_id)->get()->count() == 0) {
            return response([
                "success" => false,
                "errors" => 'Numero não esta cadastrado no app'
            ], 200);
        }

        $user = User::where('phone', $phone)->where('id', '!=', Auth::id())->first();

        if (Group::where('leader_id', $leader_id)->where('user_id', $user->id)->get()->count() > 0) {
            Group::where('leader_id', $leader_id)->where('user_id', $user->id)->delete();
            return response([
                "success" => true,
                "message" => 'Usuario removido do grupo'
            ], 200);
        } else {
            $group = new Group;
            $group->leader_id = $leader_id;
            $group->user_id = $user->id;
            $group->save();

            return response([
                "success" => true,
                "message" => 'Usuario adicionado ao grupo'
            ], 200);
        }

        
        
    }

    function exit(Request $request) {
        $request->validate([
            'leader_id' => 'required|exists:users,id'
        ]);

        Group::where('leader_id', $request->leader_id)->where('user_id', Auth::id())->delete();

        return response([
            "success" => true,
            "message" => 'Saiu do grupo com sucesso!!'
        ], 200);
    }
}
