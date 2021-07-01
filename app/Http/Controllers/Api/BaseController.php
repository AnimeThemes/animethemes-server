<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Api\QueryParser;
use App\Http\Controllers\Controller;

/**
 * Class BaseController.
 */
abstract class BaseController extends Controller
{
    /**
     * Resolves include paths and field sets.
     *
     * @var QueryParser
     */
    protected QueryParser $parser;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->parser = QueryParser::make(request()->all());
    }
}
