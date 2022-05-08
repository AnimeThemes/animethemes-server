<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Config\WikiRequest;
use App\Http\Resources\Config\Resource\WikiResource;
use Illuminate\Http\JsonResponse;
use Spatie\RouteDiscovery\Attributes\Route;

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
    #[Route(fullUri: 'config/wiki', name: 'config.wiki.show')]
    public function show(WikiRequest $request): JsonResponse
    {
        $resource = new WikiResource($request->getQuery());

        return $resource->toResponse($request);
    }
}
