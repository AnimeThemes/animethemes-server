<?php

declare(strict_types=1);

namespace App\Http\Controllers\Document;

use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CommunityController.
 */
class CommunityController extends DocumentController
{
    /**
     * Display the Community Index document.
     *
     * @return View
     * @throws HttpException
     * @throws NotFoundHttpException
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
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function show(string $docName): View
    {
        return $this->displayMarkdownDocument('community/'.$docName);
    }
}
