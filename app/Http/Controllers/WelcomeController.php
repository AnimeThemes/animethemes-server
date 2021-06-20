<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Admin\Announcement;
use Illuminate\View\View;

/**
 * Class WelcomeController.
 */
class WelcomeController extends Controller
{
    /**
     * Displays home page.
     *
     * @return  View
     */
    public function show(): View
    {
        return view('welcome', [
            'announcements' => Announcement::all(),
        ]);
    }
}
