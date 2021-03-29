<?php

namespace App\Http\Controllers\Document;

use App\Concerns\Http\Controllers\DisplaysMarkdownDocument;
use App\Http\Controllers\Controller;

class FaqController extends Controller
{
    use DisplaysMarkdownDocument;

    /**
     * Show the FAQ for the application.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return $this->displayMarkdownDocument('faq');
    }
}
