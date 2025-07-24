<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Http\Api\InteractsWithSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Auth\Authenticate;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Support\Str;

abstract class BaseController extends Controller implements InteractsWithSchema
{
    public function __construct(string $model, string $parameter)
    {
        $this->authorizeResource($model, $parameter);
        $this->middleware(Authenticate::using('sanctum'))->except(['index', 'show']);
        $this->middleware(Authorize::using('restore', $parameter))->only('restore');
        $this->middleware(Authorize::using('forceDelete', $parameter))->only('forceDelete');
    }

    /**
     * Get the underlying schema.
     */
    public function schema(): Schema
    {
        $schemaClass = Str::of(get_class($this))
            ->replace('Controllers\\Api', 'Api\\Schema')
            ->replace('Controller', 'Schema')
            ->__toString();

        return new $schemaClass();
    }
}
