<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

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
use App\Http\Resources\Wiki\Collection\EntryCollection;
use App\Http\Resources\Wiki\Resource\EntryJsonResource;
use App\Models\Wiki\Entry;
use Illuminate\Http\JsonResponse;

class EntryController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Entry::class, 'animethemeentry');
    }

    public function index(IndexRequest $request, IndexAction $action): EntryCollection
    {
        $query = new Query($request->validated());

        $entries = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Entry::query(), $query, $request->schema());

        return new EntryCollection($entries, $query);
    }

    /**
     * @param  StoreAction<Entry>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): EntryJsonResource
    {
        $entry = $action->store(Entry::query(), $request->validated());

        return new EntryJsonResource($entry, new Query());
    }

    public function show(ShowRequest $request, Entry $animethemeentry, ShowAction $action): EntryJsonResource
    {
        $query = new Query($request->validated());

        $show = $action->show($animethemeentry, $query, $request->schema());

        return new EntryJsonResource($show, $query);
    }

    public function update(UpdateRequest $request, Entry $animethemeentry, UpdateAction $action): EntryJsonResource
    {
        $updated = $action->update($animethemeentry, $request->validated());

        return new EntryJsonResource($updated, new Query());
    }

    public function destroy(Entry $animethemeentry, DestroyAction $action): EntryJsonResource
    {
        $deleted = $action->destroy($animethemeentry);

        return new EntryJsonResource($deleted, new Query());
    }

    public function restore(Entry $animethemeentry, RestoreAction $action): EntryJsonResource
    {
        $restored = $action->restore($animethemeentry);

        return new EntryJsonResource($restored, new Query());
    }

    public function forceDelete(Entry $animethemeentry, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($animethemeentry);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
