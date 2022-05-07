<?php

namespace App\Http\Controllers;

use App\Mail\SendCancelClass;
use App\Mail\SendScheduleClass;
use App\Models\Package;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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

                $user = User::find($request->user_id);
                $teacher = User::where('name', $request->teacher)->where('type', 'Professor')->first();

                $emailData = [
                    'title' => 'Class schedule',
                    'name' => $user->name,
                    'day' => $request->day,
                    'start_time' => $request->start_hour,
                    'end_time' => $request->start_hour + 1,
                ];

                Mail::to($user->email)->cc($teacher->email)->send(new SendScheduleClass($emailData));

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

    public function next_class(int $user_id)
    {
        $schedule = Schedule::where('user_id', $user_id)->where('situation', 1)->first();
        if(is_null($schedule)) {
            return response()->json([], 203);
        }
        return response()->json([$schedule], 200);
    }

    public function last_class(int $user_id)
    {
        $schedule = Schedule::where('user_id', $user_id)->where('situation', 2)->latest('id')->first();
        if(is_null($schedule)) {
            return response()->json([], 203);
        }
        return response()->json([$schedule], 200);
    }

    public function by_user($id)
    {
        $schedule = Schedule::where('user_id', $id)->get();

        if(is_null($schedule))
        {
            return response()->json('', 204);
        }

        return response()->json($schedule, 200);
    }

    public function cancel(Request $request)
    {
        $schedule = Schedule::with('user')->find($request->id);

        if($schedule->situation == 2 || $schedule->situation == 3) {

            return response()->json([], 203);

        } else {

            $today = Carbon::now();
            $dateCheck = $schedule->day;
            $interval = $today->diff($dateCheck);

            $emailData = [
                'title' => 'Class cancelled by the student',
                'name' => $schedule->user->name,
                'day' => $schedule->day,
                'start_time' => $schedule->start_hour,
                'end_time' => $schedule->start_hour + 1,
            ];
            $data = [
                'situation' => 3,
            ];

            if($interval->format('%a') != 0) {

                $schedule->fill($data);
                $schedule->save();

                $package = Package::where('user_id', $schedule->user_id)->where('situation', 1)->first();
                $package->update([
                    'hour_credit' => $package->hour_credit + 1,
                    'hour_consumed' => $package->hour_consumed - 1,
                ]);

                Mail::to($schedule->user->email)->send(new SendCancelClass($emailData));

                return response()->json($schedule, 201);
            }

            return response()->json([], 209);
        }
    }

    public function cancel_confirm(Request $request)
    {
        $schedule = Schedule::with('user')->find($request->id);
        $teacher = User::where('name', $schedule->teacher)->where('type', 'Professor')->first();

        $emailData = [
            'title' => 'Class cancelled by the student',
            'name' => $schedule->user->name,
            'day' => $schedule->day,
            'start_time' => $schedule->start_hour,
            'end_time' => $schedule->start_hour + 1,
        ];
        $data = [
            'situation' => 3,
        ];

        $schedule->fill($data);
        $schedule->save();

        Mail::to($schedule->user->email)->cc($teacher->email)->send(new SendCancelClass($emailData));

        return response()->json($schedule, 201);
    }

    public function check_schedule($teacher, $date)
    {
        $schedule = Schedule::where('teacher', $teacher)->where('day', $date)->where('situation', '!=', 3)->orderBy('day', 'desc');

        if($schedule->count() == 0) {
            return response()->json(DB::table('office_hours')->get(), 200);
        } else {
            $startHour = array();
            $endHour = array();

            foreach($schedule->get() as $value) {
                $startHour[] = $value->start_hour;
                $endHour[] = $value->end_hour;
            }

            $timeFree = DB::table('office_hours')->whereNotIn('start_hour', $startHour)->whereNotIn('end_hour', $endHour);

            if($timeFree->count() == 0) {
                return response()->json([
                    array(
                        'id' => 0,
                        'start_hour' => 0,
                        'end_hour' => 0
                    )], 209);
            }

            return response()->json($timeFree->get(), 200);
        }
    }

    public function appraisal_by_user($id)
    {
        $schedule = Schedule::where('user_id', $id)->where('situation', 2)->get();

        if(is_null($schedule))
        {
            return response()->json('', 204);
        }

        return response()->json($schedule, 200);
    }
}
