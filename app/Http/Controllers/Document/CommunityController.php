<?php

namespace App\Http\Controllers\Document;

use App\Concerns\Http\Controllers\DisplaysMarkdownDocument;
use App\Http\Controllers\Controller;

class CommunityController extends Controller
{
    use DisplaysMarkdownDocument;

    /**
     * Display the Community Index document.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->displayMarkdownDocument('community/index');
    }

    /**
     * Display the Community document.
     *
     * @return \Illuminate\View\View
     */
    public function show($docName)
    {
        return $this->displayMarkdownDocument('community/'.$docName);
    }
}
