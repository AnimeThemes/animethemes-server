<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Anime\Theme;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Anime\Theme\Collection\EntryCollection;
use App\Http\Resources\Wiki\Anime\Theme\Resource\EntryResource;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class EntryController.
 */
class EntryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if ($this->query->hasSearchCriteria()) {
            return EntryCollection::performSearch($this->query, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return EntryCollection::performQuery($this->query)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param AnimeThemeEntry $entry
     * @return JsonResponse
     */
    public function show(Request $request, AnimeThemeEntry $entry): JsonResponse
    {
        $resource = EntryResource::performQuery($entry, $this->query);

        return $resource->toResponse($request);
    }
}
