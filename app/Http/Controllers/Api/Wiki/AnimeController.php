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
use App\Http\Resources\Wiki\Collection\AnimeCollection;
use App\Http\Resources\Wiki\Resource\AnimeJsonResource;
use App\Models\Wiki\Anime;
use Illuminate\Http\JsonResponse;

class AnimeController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Anime::class, 'anime');
    }

    public function index(IndexRequest $request, IndexAction $action): AnimeCollection
    {
        $query = new Query($request->validated());

        $anime = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Anime::query(), $query, $request->schema());

        return new AnimeCollection($anime, $query);
    }

    /**
     * @param  StoreAction<Anime>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): AnimeJsonResource
    {
        $anime = $action->store(Anime::query(), $request->validated());

        return new AnimeJsonResource($anime, new Query());
    }

    public function show(ShowRequest $request, Anime $anime, ShowAction $action): AnimeJsonResource
    {
        $query = new Query($request->validated());

        $show = $action->show($anime, $query, $request->schema());

        return new AnimeJsonResource($show, $query);
    }

    public function update(UpdateRequest $request, Anime $anime, UpdateAction $action): AnimeJsonResource
    {
        $updated = $action->update($anime, $request->validated());

        return new AnimeJsonResource($updated, new Query());
    }

    public function destroy(Anime $anime, DestroyAction $action): AnimeJsonResource
    {
        $deleted = $action->destroy($anime);

        return new AnimeJsonResource($deleted, new Query());
    }

    public function restore(Anime $anime, RestoreAction $action): AnimeJsonResource
    {
        $restored = $action->restore($anime);

        return new AnimeJsonResource($restored, new Query());
    }

    public function forceDelete(Anime $anime, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($anime);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
