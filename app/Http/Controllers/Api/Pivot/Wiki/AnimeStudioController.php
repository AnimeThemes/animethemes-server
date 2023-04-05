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
use App\Http\Resources\Pivot\Wiki\Resource\AnimeStudioResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Http\JsonResponse;

/**
 * Class AnimeStudioController.
 */
class AnimeStudioController extends PivotController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Anime::class, 'anime', Studio::class, 'studio');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return AnimeStudioCollection
     */
    public function index(IndexRequest $request, IndexAction $action): AnimeStudioCollection
    {
        $query = new Query($request->validated());

        $resources = $action->index(AnimeStudio::query(), $query, $request->schema());

        return new AnimeStudioCollection($resources, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  Anime  $anime
     * @param  Studio  $studio
     * @param  StoreAction  $action
     * @return AnimeStudioResource
     */
    public function store(StoreRequest $request, Anime $anime, Studio $studio, StoreAction $action): AnimeStudioResource
    {
        $validated = array_merge(
            $request->validated(),
            [
                AnimeStudio::ATTRIBUTE_ANIME => $anime->getKey(),
                AnimeStudio::ATTRIBUTE_STUDIO => $studio->getKey(),
            ]
        );

        $animeStudio = $action->store(AnimeStudio::query(), $validated);

        return new AnimeStudioResource($animeStudio, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Anime  $anime
     * @param  Studio  $studio
     * @param  ShowAction  $action
     * @return AnimeStudioResource
     */
    public function show(ShowRequest $request, Anime $anime, Studio $studio, ShowAction $action): AnimeStudioResource
    {
        $animeStudio = AnimeStudio::query()
            ->where(AnimeStudio::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeStudio::ATTRIBUTE_STUDIO, $studio->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($animeStudio, $query, $request->schema());

        return new AnimeStudioResource($show, $query);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Anime  $anime
     * @param  Studio  $studio
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
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
