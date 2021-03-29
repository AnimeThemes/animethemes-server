<?php

namespace App\Http\Controllers\Document;

use App\Concerns\Http\Controllers\DisplaysMarkdownDocument;
use App\Http\Controllers\Controller;

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
