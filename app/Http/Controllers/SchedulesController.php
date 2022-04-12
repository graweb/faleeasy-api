<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Schedule;
use Illuminate\Http\Request;

class SchedulesController extends Controller
{
    public function index(Request $request)
    {
        return Schedule::whereIn('situation', [0, 1])->with('user')->paginate($request->per_page);
    }

    public function store(Request $request)
    {
        $package = Package::where('user_id', $request->user_id)->where('situation', 1)->where('hour_credit', '>', 0)->first();
        $schedule = Schedule::where('day', $request->day)->where('start_hour', $request->start_hour)->get();

        if(is_null($package)) {
            return Response()->json('Saldo de horas indisponível', 409);
        } else {
            if(count($schedule) == 0) {

                $data = [
                    'user_id' => $request->user_id,
                    'teacher' => $request->teacher,
                    'day' => $request->day,
                    'start_hour' => $request->start_hour,
                    'end_hour' => $request->start_hour + 1,
                    'situation' => 0
                ];

                $package->hour_credit -= 1;
                $package->hour_consumed += 1;
                $package->save();

                return Response()->json(Schedule::create($data), 201);
            } else {
                return Response()->json('Esse horário já está agendado', 409);
            }
        }
    }

    public function show(int $id)
    {
        $schedule = Schedule::find($id);

        if(is_null($schedule))
        {
            return response()->json('', 204);
        }

        return response()->json($schedule);
    }

    public function update(int $id, Request $request)
    {
        $schedule = Schedule::find($id);
        if(is_null($schedule))
        {
            return response()->json(['erro' => 'Not found'], 404);
        }

        if(is_null($request->situation) && is_null($request->subject)) {
            $data = [
                'user_id' => $request->user_id,
                'teacher' => $request->teacher,
                'day' => $request->day,
                'start_hour' => $request->start_hour,
                'end_hour' => $request->start_hour + 1,
            ];
        } else if(!is_null($request->situation) && is_null($request->subject)) {
            $data = [
                'situation' => $request->situation,
            ];
        } else {
            $data = [
                'subject' => $request->subject,
                'speak' => $request->speak,
                'listen' => $request->listen,
                'read' => $request->read,
                'write' => $request->write,
                'situation' => '2',
            ];
        }

        $schedule->fill($data);
        $schedule->save();

        return $schedule;
    }

    public function destroy(int $id)
    {
        $numberDataRemoved = Schedule::destroy($id);
        if($numberDataRemoved === 0)
        {
            return response()->json(['erro' => 'Not found'], 404);
        }

        return response()->json(['response' => 'Removed'], 200);
    }
}
