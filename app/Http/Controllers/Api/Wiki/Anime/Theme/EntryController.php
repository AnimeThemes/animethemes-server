<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Anime\Theme;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\ForceDeleteAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\RestoreAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
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
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::class, 'animethemeentry');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $videos = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(AnimeThemeEntry::query(), $query, $request->schema());

        $collection = new EntryCollection($videos, $query);

        return $collection->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  StoreAction  $action
     * @return JsonResponse
     */
    public function store(StoreRequest $request, StoreAction $action): JsonResponse
    {
        $entry = $action->store(AnimeThemeEntry::query(), $request->validated());

        $resource = new EntryResource($entry, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  AnimeThemeEntry  $animethemeentry
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, AnimeThemeEntry $animethemeentry, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($animethemeentry, $query, $request->schema());

        $resource = new EntryResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  AnimeThemeEntry  $animethemeentry
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, AnimeThemeEntry $animethemeentry, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($animethemeentry, $request->validated());

        $resource = new EntryResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  AnimeThemeEntry  $animethemeentry
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, AnimeThemeEntry $animethemeentry, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($animethemeentry);

        $resource = new EntryResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  AnimeThemeEntry  $animethemeentry
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, AnimeThemeEntry $animethemeentry, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($animethemeentry);

        $resource = new EntryResource($restored, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  AnimeThemeEntry  $animethemeentry
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(AnimeThemeEntry $animethemeentry, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($animethemeentry);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
