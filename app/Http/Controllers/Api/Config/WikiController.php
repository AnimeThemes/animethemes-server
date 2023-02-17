<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Config;

use App\Contracts\Http\Api\InteractsWithSchema;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Config\WikiSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\Config\Resource\WikiResource;
use Illuminate\Http\JsonResponse;

/**
 * Class WikiController.
 */
class WikiController extends Controller implements InteractsWithSchema
{
    /**
     * Wiki resource.
     *
     * @param  ShowRequest  $request
     * @return JsonResponse
     */
    public function show(ShowRequest $request): JsonResponse
    {
        $query = new Query($request->validated());

        $resource = new WikiResource($query);

        return $resource->toResponse($request);
    }

    /**
     * Get the underlying schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new WikiSchema();
    }
}
