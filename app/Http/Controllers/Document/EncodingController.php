<?php

namespace App\Http\Controllers\Document;

use App\Concerns\Http\Controllers\DisplaysMarkdownDocument;
use App\Http\Controllers\Controller;

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
