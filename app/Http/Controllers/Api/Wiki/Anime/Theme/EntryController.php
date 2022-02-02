<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Anime\Theme;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Anime\Theme\Entry\EntryIndexRequest;
use App\Http\Requests\Api\Wiki\Anime\Theme\Entry\EntryShowRequest;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Http\JsonResponse;

/**
 * Class EntryController.
 */
class EntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  EntryIndexRequest  $request
     * @return JsonResponse
     */
    public function index(EntryIndexRequest $request): JsonResponse
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
     * @param  EntryShowRequest  $request
     * @param  AnimeThemeEntry  $entry
     * @return JsonResponse
     */
    public function show(EntryShowRequest $request, AnimeThemeEntry $entry): JsonResponse
    {
        $resource = $request->getQuery()->show($entry);

        return $resource->toResponse($request);
    }
}
