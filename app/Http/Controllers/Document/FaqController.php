<?php

declare(strict_types=1);

namespace App\Http\Controllers\Document;

use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class FaqController.
 */
class FaqController extends DocumentController
{
    /**
     * Show the FAQ for the application.
     *
     * @return View
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function show(): View
    {
        return $this->displayMarkdownDocument('faq');
    }
}
