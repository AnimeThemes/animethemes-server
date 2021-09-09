<?php

declare(strict_types=1);

namespace App\Http\Controllers\Document;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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
     * @return View|Factory
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function show(): View|Factory
    {
        return $this->displayMarkdownDocument('faq');
    }
}
