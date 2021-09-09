<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Wiki\ExternalResource\ExternalResourceIndexRequest;
use App\Http\Requests\Api\Wiki\ExternalResource\ExternalResourceShowRequest;
use App\Http\Resources\Wiki\Collection\ExternalResourceCollection;
use App\Http\Resources\Wiki\Resource\ExternalResourceResource;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\JsonResponse;

/**
 * Class ExternalResourceController.
 */
class ExternalResourceController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param  ExternalResourceIndexRequest  $request
     * @return JsonResponse
     */
    public function index(ExternalResourceIndexRequest $request): JsonResponse
    {
        $resources = ExternalResourceCollection::performQuery($this->query);

        return $resources->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ExternalResourceShowRequest  $request
     * @param  ExternalResource  $resource
     * @return JsonResponse
     */
    public function show(ExternalResourceShowRequest $request, ExternalResource $resource): JsonResponse
    {
        $resource = ExternalResourceResource::performQuery($resource, $this->query);

        return $resource->toResponse($request);
    }
}
