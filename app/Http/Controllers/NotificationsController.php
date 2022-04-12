<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        return Notification::with('user')->paginate($request->per_page);
    }

    public function store(Request $request)
    {
        if($request->all_students) {
            $users = User::where('type', 'Aluno')->get();

            foreach($users as $user) {
                $data = [
                    'user_id' => $user->id,
                    'subject' => $request->subject,
                    'message' => $request->message,
                    'situation' => 0
                ];

                Notification::create($data);
            }

            return Response()->json(['response' => 'Notifications was sent successfully'], 201);

        } else {
            $data = [
                'user_id' => $request->user_id,
                'subject' => $request->subject,
                'message' => $request->message,
                'situation' => 0
            ];
            return Response()->json(Notification::create($data), 201);
        }
    }

    public function show(int $id)
    {
        $schedule = Notification::find($id);

        if(is_null($schedule))
        {
            return response()->json('', 204);
        }

        return response()->json($schedule);
    }

    public function update(int $id, Request $request)
    {
        $schedule = Notification::find($id);
        if(is_null($schedule))
        {
            return response()->json(['erro' => 'Not found'], 404);
        }

        $data = [
            'subject' => $request->subject,
            'message' => $request->message,
        ];

        $schedule->fill($data);
        $schedule->save();

        return $schedule;
    }

    public function destroy(int $id)
    {
        $numberDataRemoved = Notification::destroy($id);
        if($numberDataRemoved === 0)
        {
            return response()->json(['erro' => 'Not found'], 404);
        }

        return response()->json(['response' => 'Removed'], 200);
    }
}
