<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\ExternalResource\ExternalResourceIndexRequest;
use App\Http\Requests\Api\Wiki\ExternalResource\ExternalResourceShowRequest;
use App\Models\Wiki\ExternalResource;
use Illuminate\Http\JsonResponse;

/**
 * Class ExternalResourceController.
 */
class ExternalResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  ExternalResourceIndexRequest  $request
     * @return JsonResponse
     */
    public function index(ExternalResourceIndexRequest $request): JsonResponse
    {
        $resources = $request->getQuery()->index();

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
        $resourceResource = $request->getQuery()->show($resource);

        return $resourceResource->toResponse($request);
    }
}
