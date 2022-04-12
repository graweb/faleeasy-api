<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ClassBlockController extends Controller
{
    public function index()
    {
        $schedule = Schedule::where('situation', 4)->get();

        if(is_null($schedule))
        {
            return response()->json('', 204);
        }

        return response()->json($schedule);
    }

    public function store(Request $request)
    {
        $schedule = Schedule::where('day', $request->day)->where('start_hour', $request->start_hour)->get();

        if(count($schedule) == 0) {
            $data = [
                'teacher' => $request->teacher,
                'day' => $request->day,
                'start_hour' => $request->start_hour,
                'end_hour' => $request->start_hour + 1,
                'situation' => 4
            ];
            return Response()->json(Schedule::create($data), 201);
        } else {
            return Response()->json('Esse horário já está agendado', 409);
        }
    }
}
