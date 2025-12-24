<?php

namespace App\Http\Controllers\Translator;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    protected $middleware = [
        'translator'
    ];

    public function index()
    {
        return view('translator.dashboard');
    }
}
