<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Config\WikiShowRequest;
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
     * @param  WikiShowRequest  $request
     * @return JsonResponse
     */
    public function show(WikiShowRequest $request): JsonResponse
    {
        $resource = WikiResource::make($request->getQuery());

        return $resource->toResponse($request);
    }
}
