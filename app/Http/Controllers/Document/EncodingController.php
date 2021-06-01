<?php

declare(strict_types=1);

namespace App\Http\Controllers\Document;

use App\Concerns\Http\Controllers\DisplaysMarkdownDocument;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Class EncodingController
 * @package App\Http\Controllers\Document
 */
class EncodingController extends Controller
{
    use DisplaysMarkdownDocument;

    /**
     * Display the Encoding Index document.
     *
     * @return View
     */
    public function index(): View
    {
        return $this->displayMarkdownDocument('encoding/index');
    }

    /**
     * Display the Encoding document.
     *
     * @param string $docName
     * @return View
     */
    public function show(string $docName): View
    {
        return $this->displayMarkdownDocument('encoding/'.$docName);
    }
}
