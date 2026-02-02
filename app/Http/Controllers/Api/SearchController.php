<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\Http\Api\InteractsWithSchema;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\SearchSchema;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SearchRequest;
use App\Http\Resources\SearchJsonResource;

class SearchController extends Controller implements InteractsWithSchema
{
    /**
     * Search resource.
     */
    public function show(SearchRequest $request): SearchJsonResource
    {
        $query = new Query($request->validated());

        return new SearchJsonResource($query);
    }

    /**
     * Get the underlying schema.
     */
    public function schema(): Schema
    {
        return new SearchSchema();
    }
}
