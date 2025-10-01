<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pivot;

use App\Contracts\Http\Api\InteractsWithSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Auth\Authenticate;
use App\Http\Middleware\Models\Pivot\AuthorizesPivot;
use Illuminate\Support\Str;

abstract class PivotController extends Controller implements InteractsWithSchema
{
    public function __construct(string $foreignModel, string $foreignParameter, string $relatedModel, string $relatedParameter)
    {
        $this->middleware(AuthorizesPivot::class.":{$foreignModel},{$foreignParameter},{$relatedModel},{$relatedParameter}");
        $this->middleware(Authenticate::using('sanctum'))->except(['index', 'show']);
    }

    /**
     * Get the underlying schema.
     */
    public function schema(): Schema
    {
        $schemaClass = Str::of(static::class)
            ->replace('Controllers\\Api', 'Api\\Schema')
            ->replace('Controller', 'Schema')
            ->__toString();

        return new $schemaClass();
    }
}
