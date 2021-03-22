<?php

namespace App\Http\Controllers;

use App\Concerns\Http\Controllers\DisplaysMarkdownDocument;

class GuidelinesController extends Controller
{
    use DisplaysMarkdownDocument;

    /**
     * Display the Guidelines Index document.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->displayMarkdownDocument('guidelines/index');
    }

    /**
     * Display the Guidelines document.
     *
     * @return \Illuminate\View\View
     */
    public function show($docName)
    {
        return $this->displayMarkdownDocument('guidelines/'.$docName);
    }
}
