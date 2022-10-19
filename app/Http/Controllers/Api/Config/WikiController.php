<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Config\WikiRequest;
use App\Http\Resources\Config\Resource\WikiResource;
use Illuminate\Http\JsonResponse;

/**
 * Class WikiController.
 */
class WikiController extends Controller
{
    /**
     * Wiki resource.
     *
     * @param  WikiRequest  $request
     * @return JsonResponse
     */
    public function show(WikiRequest $request): JsonResponse
    {
        $resource = new WikiResource($request->getQuery());

        return $resource->toResponse($request);
    }
}
