<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * Class BaseController.
 */
abstract class BaseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  string  $model
     * @param  string  $parameter
     */
    public function __construct(string $model, string $parameter)
    {
        $this->authorizeResource($model, $parameter);
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->middleware("can:restore,$parameter")->only('restore');
        $this->middleware("can:forceDelete,$parameter")->only('forceDelete');
    }
}
