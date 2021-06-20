<?php

declare(strict_types=1);

namespace App\Http\Controllers\Document;

use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class EncodingController.
 */
class EncodingController extends DocumentController
{
    /**
     * Display the Encoding Index document.
     *
     * @return View
     * @throws HttpException
     * @throws NotFoundHttpException
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
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function show(string $docName): View
    {
        return $this->displayMarkdownDocument('encoding/'.$docName);
    }
}
