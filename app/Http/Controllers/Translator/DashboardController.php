<?php

namespace App\Http\Controllers\Translator;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return redirect()->route('translator.ui-matrix.index');
    }
}
