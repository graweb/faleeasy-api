<?php

namespace App\Http\Controllers;

use App\Models\User;

class PeopleController extends Controller
{
    public function show(string $type)
    {
        $user = User::where('type', $type)->get();

        if(is_null($user))
        {
            return response()->json('', 204);
        }

        return response()->json($user);
    }
}
