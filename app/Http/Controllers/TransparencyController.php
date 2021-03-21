<?php

namespace App\Http\Controllers;

class TransparencyController extends Controller
{
    /**
     * Show the transparency for the application.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('transparency');
    }
}
