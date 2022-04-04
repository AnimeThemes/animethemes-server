<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\SearchRequest;
use App\Http\Resources\Wiki\Resource\SearchResource;
use Illuminate\Http\JsonResponse;
use Spatie\RouteDiscovery\Attributes\Route;

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
    #[Route(fullUri: 'search', name: 'search.show')]
    public function show(SearchRequest $request): JsonResponse
    {
        $resource = SearchResource::make($request->getQuery());

        return $resource->toResponse($request);
    }
}
