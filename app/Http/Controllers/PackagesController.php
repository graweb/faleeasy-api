<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PackagesController extends Controller
{
    public function index(Request $request)
    {
        return Package::whereIn('situation', [0, 1])->with('user')->paginate($request->per_page);
    }

    public function store(Request $request)
    {
        $package = Package::where('user_id', $request->user_id)->where('situation', 1)->first();

        switch($request->plan) {
            case '1 hora - Individual':
                $credit = '1';
                break;
            case '4 horas - Individual 1x':
                $credit = '4';
                break;
            case '8 horas - Individual 1x':
                $credit = '8';
                break;
            case '4 horas - Individual 1x - Recorrente':
                $credit = '4';
                break;
            case '8 horas - Individual 1x - Recorrente':
                $credit = '8';
                break;
        }

        if(is_null($package)) {
            $data = [
                'user_id' => $request->user_id,
                'hour_credit' => $credit,
                'hour_consumed' => 0,
                'situation' => 0,
                'expiration_date' => date('Y-m-d', strtotime('+30 days', strtotime(Date('Y-m-d')))),
            ];
        } else {
            $data = [
                'user_id' => $request->user_id,
                'hour_credit' => $credit + $package->hour_credit,
                'hour_consumed' => $package->hour_consumed,
                'situation' => 0,
                'expiration_date' => date('Y-m-d', strtotime('+30 days', strtotime(Date('Y-m-d')))),
            ];

            $package->situation = 2;
            $package->save();
        }

        return Response()->json(Package::create($data), 201);
    }

    public function show(int $id)
    {
        $package = Package::find($id);

        if(is_null($package))
        {
            return response()->json('', 204);
        }

        return response()->json($package);
    }

    public function update(int $id, Request $request)
    {
        $package = Package::find($id);
        if(is_null($package))
        {
            return response()->json(['erro' => 'Not found'], 404);
        }

        if(is_null($request->situation) && is_null($request->subject)) {
            $data = [
                'user_id' => $request->user_id,
                'plan' => $request->plan,
                'hour_credit' => $request->hour_credit,
                'hour_consumed' => $request->hour_consumed,
            ];
        } else {
            $data = [
                'situation' => $request->situation,
            ];
        }

        $package->fill($data);
        $package->save();

        return $package;
    }

    public function destroy(int $id)
    {
        $numberDataRemoved = Package::destroy($id);
        if($numberDataRemoved === 0)
        {
            return response()->json(['erro' => 'Not found'], 404);
        }

        return response()->json(['response' => 'Removed'], 200);
    }

    public function balance(int $user_id)
    {
        $balance = Package::where('user_id', $user_id)->where('situation', 1)->first();

        if(is_null($balance)) {
            return response()->json(['hour_credit' => 0], 200);
        }

        return response()->json(['hour_credit' => $balance->hour_credit], 200);
    }

    public function by_user($id)
    {
        $package = Package::where('user_id', $id)->whereIn('situation', [0, 1])->get();

        if(is_null($package))
        {
            return response()->json('', 204);
        }

        return response()->json($package, 200);
    }
}
