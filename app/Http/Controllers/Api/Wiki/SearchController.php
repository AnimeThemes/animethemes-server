<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Api\Query;
use App\Http\Controllers\Controller;
use App\Http\Resources\Wiki\Resource\SearchResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class SearchController.
 */
class SearchController extends Controller
{
    /**
     * Search resource.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        $query = Query::make($request->only(Query::parameters()));

        $resource = SearchResource::make($query);

        return $resource->toResponse($request);
    }
}
