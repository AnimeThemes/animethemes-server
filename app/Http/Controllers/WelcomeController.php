<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Admin\Announcement;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

/**
 * Class WelcomeController.
 */
class WelcomeController extends Controller
{
    /**
     * Displays home page.
     *
     * @return View|Factory
     */
    public function show(): View|Factory
    {
        return view('welcome', [
            'announcements' => Announcement::all([Announcement::ATTRIBUTE_CONTENT]),
        ]);
    }
}
