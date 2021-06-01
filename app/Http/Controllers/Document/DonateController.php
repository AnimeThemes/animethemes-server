<?php declare(strict_types=1);

namespace App\Http\Controllers\Document;

use App\Concerns\Http\Controllers\DisplaysMarkdownDocument;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Class DonateController
 * @package App\Http\Controllers\Document
 */
class DonateController extends Controller
{
    use DisplaysMarkdownDocument;

    /**
     * Show the donate document for the application.
     *
     * @return View
     */
    public function show(): View
    {
        return $this->displayMarkdownDocument('donate');
    }
}
