<?php

namespace App\Http\Controllers;

use App\Mail\SendNewUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'change_password' => 'required',
            'type' => 'required',
        ]);

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => $request->type,
            'change_password' => $request->change_password,
        ]);
    }

    public function show($id)
    {
        return User::find($id);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if(is_null($request->password)) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'type' => $request->type,
                'change_password' => $request->change_password,
            ]);
        } else {
            $user->update([
                'password' => Hash::make($request->password),
                'change_password' => 'Não',
            ]);
        }
        return $user;
    }

    public function destroy($id)
    {
        return User::destroy($id);
    }

    public function new_user(Request $request)
    {
        $users = User::where('email', $request->email)->first();

        if(is_null($users)) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'type' => 'Aluno',
                'change_password' => 'Sim',
            ]);

            $data = [
                'title' => 'Welcome',
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ];

            Mail::to($request->email)->send(new SendNewUser($data));

            return Response()->json($user, 201);
        }

        return Response()->json('User already exists', 409);
    }

    public function reset_password(Request $request)
    {
        $users = User::where('email', $request->email)->first();

        if(!is_null($users)) {
            $user = User::where('email', $request->email)->update([
                'password' => Hash::make($request->password),
                'change_password' => 'Sim',
            ]);

            $data = [
                'title' => 'Change password',
                'name' => $users->name,
                'email' => $request->email,
                'password' => $request->password,
            ];

            Mail::to($request->email)->send(new SendNewUser($data));

            return Response()->json('Password changed', 201);
        }

        return Response()->json('This email does not exists.', 409);
    }

    public function list_teachers()
    {
        $users = User::where('type', 'Professor')->get();
        return Response()->json($users, 200);
    }
}
