<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Config\FlagsRequest;
use App\Http\Resources\Config\Resource\FlagsResource;
use Illuminate\Http\JsonResponse;
use Spatie\RouteDiscovery\Attributes\Route;

/**
 * Class FlagsController.
 */
class FlagsController extends Controller
{
    /**
     * Flags resource.
     *
     * @param  FlagsRequest  $request
     * @return JsonResponse
     */
    #[Route(fullUri: 'config/flags', name: 'config.flags.show')]
    public function show(FlagsRequest $request): JsonResponse
    {
        $resource = FlagsResource::make($request->getQuery());

        return $resource->toResponse($request);
    }
}
