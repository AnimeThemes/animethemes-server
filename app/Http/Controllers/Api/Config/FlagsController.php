<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Config\FlagsShowRequest;
use App\Http\Resources\Config\Resource\FlagsResource;
use Illuminate\Http\JsonResponse;

/**
 * Class FlagsController.
 */
class FlagsController extends Controller
{
    /**
     * Flags resource.
     *
     * @param  FlagsShowRequest  $request
     * @return JsonResponse
     */
    public function show(FlagsShowRequest $request): JsonResponse
    {
        $resource = FlagsResource::make($request->getQuery());

        return $resource->toResponse($request);
    }
}
