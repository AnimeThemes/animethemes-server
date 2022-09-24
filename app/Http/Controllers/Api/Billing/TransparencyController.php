<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Billing\TransparencyRequest;
use App\Http\Resources\Billing\Resource\TransparencyResource;
use Illuminate\Http\JsonResponse;
use Spatie\RouteDiscovery\Attributes\Route;

/**
 * Class TransparencyController.
 */
class TransparencyController extends Controller
{
    /**
     * Show the transparency for the application.
     *
     * @param  TransparencyRequest  $request
     * @return JsonResponse
     */
    #[Route(fullUri: 'transparency', name: 'transparency.show')]
    public function show(TransparencyRequest $request): JsonResponse
    {
        $resource = new TransparencyResource(
            $request->getSelectedDate(),
            $request->getValidDates(),
            $request->getBalances(),
            $request->getTransactions()
        );

        return $resource->toResponse($request);
    }
}
