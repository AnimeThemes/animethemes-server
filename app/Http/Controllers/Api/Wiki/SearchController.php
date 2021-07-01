<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Resource\SearchResource;
use Illuminate\Http\JsonResponse;

/**
 * Class SearchController.
 */
class SearchController extends BaseController
{
    /**
     * Search resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $resource = SearchResource::make($this->parser);

        return $resource->toResponse(request());
    }
}
