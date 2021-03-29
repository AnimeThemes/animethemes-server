<?php

namespace App\Http\Controllers\Document;

use App\Concerns\Http\Controllers\DisplaysMarkdownDocument;
use App\Http\Controllers\Controller;

class DonateController extends Controller
{
    use DisplaysMarkdownDocument;

    /**
     * Show the donate document for the application.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return $this->displayMarkdownDocument('donate');
    }
}
