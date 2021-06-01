<?php

declare(strict_types=1);

namespace App\Http\Controllers\Document;

use App\Concerns\Http\Controllers\DisplaysMarkdownDocument;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Class GuidelinesController
 * @package App\Http\Controllers\Document
 */
class GuidelinesController extends Controller
{
    use DisplaysMarkdownDocument;

    /**
     * Display the Guidelines Index document.
     *
     * @return View
     */
    public function index(): View
    {
        return $this->displayMarkdownDocument('guidelines/index');
    }

    /**
     * Display the Guidelines document.
     *
     * @param string $docName
     * @return View
     */
    public function show(string $docName): View
    {
        return $this->displayMarkdownDocument('guidelines/'.$docName);
    }
}
