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
        $notification = Notification::find($id);

        if(is_null($notification))
        {
            return response()->json('', 204);
        }

        return response()->json($notification);
    }

    public function update(int $id, Request $request)
    {
        $notification = Notification::find($id);
        if(is_null($notification))
        {
            return response()->json(['erro' => 'Not found'], 404);
        }

        $data = [
            'subject' => $request->subject,
            'message' => $request->message,
        ];

        $notification->fill($data);
        $notification->save();

        return $notification;
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

    public function count_by_user($id)
    {
        $count = Notification::where('user_id', $id)->where('situation', 0)->count();
        return response()->json(['total' => $count], 200);
    }

    public function by_user($id)
    {
        $notification = Notification::where('user_id', $id)->get();

        if(is_null($notification))
        {
            return response()->json('', 204);
        }

        return response()->json($notification, 200);
    }

    public function check_read($id)
    {
        $notification = Notification::find($id);
        if(is_null($notification))
        {
            return response()->json(['erro' => 'Not found'], 404);
        }

        $data = [
            'situation' => 1,
        ];

        $notification->fill($data);
        $notification->save();

        return $notification;
    }
}
