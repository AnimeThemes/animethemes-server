<?php

declare(strict_types=1);

namespace App\Http\Controllers\Document;

use App\Concerns\Http\Controllers\DisplaysMarkdownDocument;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Class FaqController
 * @package App\Http\Controllers\Document
 */
class FaqController extends Controller
{
    use DisplaysMarkdownDocument;

    /**
     * Show the FAQ for the application.
     *
     * @return View
     */
    public function show(): View
    {
        return $this->displayMarkdownDocument('faq');
    }
}
