<?php

namespace App\Http\Controllers;

use App\Concerns\Http\Controllers\DisplaysMarkdownDocument;

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
