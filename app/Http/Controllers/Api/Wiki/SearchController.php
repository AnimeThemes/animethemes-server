<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\SearchRequest;
use App\Http\Resources\Wiki\Resource\SearchResource;
use Illuminate\Http\JsonResponse;

/**
 * Class SearchController.
 */
class SearchController extends Controller
{
    /**
     * Search resource.
     *
     * @param  SearchRequest  $request
     * @return JsonResponse
     */
    public function show(SearchRequest $request): JsonResponse
    {
        $resource = new SearchResource($request->getQuery());

        return $resource->toResponse($request);
    }
}
