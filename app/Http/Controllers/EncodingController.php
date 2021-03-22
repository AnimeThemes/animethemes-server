<?php

namespace App\Http\Controllers;

use App\Concerns\Http\Controllers\DisplaysMarkdownDocument;

class EncodingController extends Controller
{
    use DisplaysMarkdownDocument;

    /**
     * Display the Encoding Index document.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->displayMarkdownDocument('encoding/index');
    }

    /**
     * Display the Encoding document.
     *
     * @return \Illuminate\View\View
     */
    public function show($docName)
    {
        return $this->displayMarkdownDocument('encoding/'.$docName);
    }
}
