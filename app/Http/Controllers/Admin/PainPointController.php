<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PainPoint;

class PainPointController extends Controller
{
    public function index()
    {
        $painPoints = PainPoint::query()->latest('id')->paginate(30);

        return view('admin.pain-points.index', compact('painPoints'));
    }
}
