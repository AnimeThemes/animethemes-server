<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class ExternalResourceController.
 */
class ExternalResourceController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $resources = ExternalResourceCollection::performQuery($this->parser);

        return $resources->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param ExternalResource $resource
     * @return JsonResponse
     */
    public function show(Request $request, ExternalResource $resource): JsonResponse
    {
        $resource = ExternalResourceResource::performQuery($resource, $this->parser);

        return $resource->toResponse($request);
    }
}
