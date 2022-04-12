<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function packagesByStudent($id)
    {
        return Package::with('user')->where('user_id', $id)->get();
    }
}
