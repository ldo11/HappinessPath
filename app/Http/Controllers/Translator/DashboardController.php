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
        return redirect()->route('translator.language-lines.index');
    }
}
