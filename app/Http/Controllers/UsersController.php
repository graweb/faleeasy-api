<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            ]);
        }
        return $user;
    }

    public function destroy($id)
    {
        return User::destroy($id);
    }
}
