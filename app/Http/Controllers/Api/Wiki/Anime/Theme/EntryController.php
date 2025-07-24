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

class EntryController extends BaseController
{
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::class, 'animethemeentry');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexAction  $action
     */
    public function index(IndexRequest $request, IndexAction $action): EntryCollection
    {
        $query = new Query($request->validated());

        $entries = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(AnimeThemeEntry::query(), $query, $request->schema());

        return new EntryCollection($entries, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreAction<AnimeThemeEntry>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): EntryResource
    {
        $entry = $action->store(AnimeThemeEntry::query(), $request->validated());

        return new EntryResource($entry, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowAction  $action
     */
    public function show(ShowRequest $request, AnimeThemeEntry $animethemeentry, ShowAction $action): EntryResource
    {
        $query = new Query($request->validated());

        $show = $action->show($animethemeentry, $query, $request->schema());

        return new EntryResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateAction  $action
     */
    public function update(UpdateRequest $request, AnimeThemeEntry $animethemeentry, UpdateAction $action): EntryResource
    {
        $updated = $action->update($animethemeentry, $request->validated());

        return new EntryResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  DestroyAction  $action
     */
    public function destroy(AnimeThemeEntry $animethemeentry, DestroyAction $action): EntryResource
    {
        $deleted = $action->destroy($animethemeentry);

        return new EntryResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  RestoreAction  $action
     */
    public function restore(AnimeThemeEntry $animethemeentry, RestoreAction $action): EntryResource
    {
        $restored = $action->restore($animethemeentry);

        return new EntryResource($restored, new Query());
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  ForceDeleteAction  $action
     */
    public function forceDelete(AnimeThemeEntry $animethemeentry, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($animethemeentry);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
