<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Studio\StudioIndexRequest;
use App\Http\Requests\Api\Wiki\Studio\StudioShowRequest;
use App\Models\Wiki\Studio;
use Illuminate\Http\JsonResponse;

/**
 * Class StudioController.
 */
class StudioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  StudioIndexRequest  $request
     * @return JsonResponse
     */
    public function index(StudioIndexRequest $request): JsonResponse
    {
        $query = $request->getQuery();

        if ($query->hasSearchCriteria()) {
            return $query->search(PaginationStrategy::OFFSET())->toResponse($request);
        }

        return $query->index()->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  StudioShowRequest  $request
     * @param  Studio  $studio
     * @return JsonResponse
     */
    public function show(StudioShowRequest $request, Studio $studio): JsonResponse
    {
        $resource = $request->getQuery()->show($studio);

        return $resource->toResponse($request);
    }
}
