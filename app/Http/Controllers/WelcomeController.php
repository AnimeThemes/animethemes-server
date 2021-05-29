<?php

namespace App\Http\Controllers;

use App\Models\Announcement;

class WelcomeController extends Controller
{
    /**
     * Displays home page.
     *
     * @return  \Illuminate\View\View
     */
    public function show()
    {
        return view('welcome', [
            'announcements' => Announcement::all(),
        ]);
    }
}
