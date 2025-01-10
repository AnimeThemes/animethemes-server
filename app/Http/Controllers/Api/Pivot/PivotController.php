<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pivot;

use App\Contracts\Http\Api\InteractsWithSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Controllers\Controller;
use App\Http\Middleware\Auth\Authenticate;
use App\Http\Middleware\Models\Pivot\AuthorizesPivot;
use Illuminate\Support\Str;

/**
 * Class PivotController.
 */
abstract class PivotController extends Controller implements InteractsWithSchema
{
    /**
     * Create a new controller instance.
     *
     * @param  string  $foreignModel
     * @param  string  $foreignParameter
     * @param  string  $relatedModel
     * @param  string  $relatedParameter
     */
    public function __construct(string $foreignModel, string $foreignParameter, string $relatedModel, string $relatedParameter)
    {
        $this->middleware(AuthorizesPivot::class.":{$foreignModel},{$foreignParameter},{$relatedModel},{$relatedParameter}");
        $this->middleware(Authenticate::using('sanctum'))->except(['index', 'show']);
    }

    /**
     * Get the underlying schema.
     *
     * @return Schema
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
