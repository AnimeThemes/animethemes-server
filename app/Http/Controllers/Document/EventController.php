<?php declare(strict_types=1);

namespace App\Http\Controllers\Document;

use App\Concerns\Http\Controllers\DisplaysMarkdownDocument;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Class EventController
 * @package App\Http\Controllers\Document
 */
class EventController extends Controller
{
    use DisplaysMarkdownDocument;

    /**
     * Display the Event Index document.
     *
     * @return View
     */
    public function index(): View
    {
        return $this->displayMarkdownDocument('event/index');
    }

    /**
     * Display the Event document.
     *
     * @param string $docName
     * @return View
     */
    public function show(string $docName): View
    {
        return $this->displayMarkdownDocument('event/'.$docName);
    }
}
