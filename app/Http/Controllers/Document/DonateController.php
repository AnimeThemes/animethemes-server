<?php

declare(strict_types=1);

namespace App\Http\Controllers\Document;

use App\Concerns\Http\Controllers\DisplaysMarkdownDocument;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DonateController.
 */
class DonateController extends Controller
{
    use DisplaysMarkdownDocument;

    /**
     * Show the donate document for the application.
     *
     * @return View
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function show(): View
    {
        return $this->displayMarkdownDocument('donate');
    }
}
