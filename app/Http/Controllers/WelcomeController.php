<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\View\View;

/**
 * Class WelcomeController
 * @package App\Http\Controllers
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
