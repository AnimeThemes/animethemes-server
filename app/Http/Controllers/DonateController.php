<?php

namespace App\Http\Controllers;

use App\Concerns\Http\Controllers\DisplaysMarkdownDocument;

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
