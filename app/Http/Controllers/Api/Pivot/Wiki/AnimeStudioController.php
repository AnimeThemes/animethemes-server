<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pivot\Wiki;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\StoreAction;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\Pivot\PivotController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Resources\Pivot\Wiki\Collection\AnimeStudioCollection;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeStudioJsonResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Http\JsonResponse;

class AnimeStudioController extends PivotController
{
    public function __construct()
    {
        parent::__construct(Anime::class, 'anime', Studio::class, 'studio');
    }

    public function index(IndexRequest $request, IndexAction $action): AnimeStudioCollection
    {
        $query = new Query($request->validated());

        $resources = $action->index(AnimeStudio::query(), $query, $request->schema());

        return new AnimeStudioCollection($resources, $query);
    }

    /**
     * @param  StoreAction<AnimeStudio>  $action
     */
    public function store(StoreRequest $request, Anime $anime, Studio $studio, StoreAction $action): AnimeStudioJsonResource
    {
        $validated = array_merge(
            $request->validated(),
            [
                AnimeStudio::ATTRIBUTE_ANIME => $anime->getKey(),
                AnimeStudio::ATTRIBUTE_STUDIO => $studio->getKey(),
            ]
        );

        $animeStudio = $action->store(AnimeStudio::query(), $validated);

        return new AnimeStudioJsonResource($animeStudio, new Query());
    }

    public function show(ShowRequest $request, Anime $anime, Studio $studio, ShowAction $action): AnimeStudioJsonResource
    {
        $animeStudio = AnimeStudio::query()
            ->where(AnimeStudio::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeStudio::ATTRIBUTE_STUDIO, $studio->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($animeStudio, $query, $request->schema());

        return new AnimeStudioJsonResource($show, $query);
    }

    public function destroy(Anime $anime, Studio $studio, DestroyAction $action): JsonResponse
    {
        $animeStudio = AnimeStudio::query()
            ->where(AnimeStudio::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeStudio::ATTRIBUTE_STUDIO, $studio->getKey())
            ->firstOrFail();

        $action->destroy($animeStudio);

        return new JsonResponse([
            'message' => "Anime '{$studio->getName()}' has been detached from Studio '{$anime->getName()}'.",
        ]);
    }
}
