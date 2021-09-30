<?php

declare(strict_types=1);

namespace App\Http\Controllers\Document;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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
     * @return View|Factory
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function index(): View|Factory
    {
        return $this->displayMarkdownDocument('encoding/index');
    }

    /**
     * Display the Encoding document.
     *
     * @param  string  $docName
     * @return View|Factory
     *
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function show(string $docName): View|Factory
    {
        return $this->displayMarkdownDocument('encoding/'.$docName);
    }
}
