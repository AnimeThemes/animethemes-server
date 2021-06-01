<?php

declare(strict_types=1);

namespace App\Http\Controllers\Document;

use App\Concerns\Http\Controllers\DisplaysMarkdownDocument;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Class CommunityController.
 */
class CommunityController extends Controller
{
    use DisplaysMarkdownDocument;

    /**
     * Display the Community Index document.
     *
     * @return View
     */
    public function index(): View
    {
        return $this->displayMarkdownDocument('community/index');
    }

    /**
     * Display the Community document.
     *
     * @param string $docName
     * @return View
     */
    public function show(string $docName): View
    {
        return $this->displayMarkdownDocument('community/'.$docName);
    }
}
