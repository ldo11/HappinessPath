<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PainPoint;

class PainPointController extends Controller
{
    protected $middleware = [
        'admin'
    ];

    public function index()
    {
        $painPoints = PainPoint::query()->orderBy('category')->orderBy('name')->paginate(50);

        return view('admin.pain-points.index', compact('painPoints'));
    }
}
