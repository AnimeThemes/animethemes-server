<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pivot\List;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\StoreAction;
use App\Constants\Config\FlagConstants;
use App\Enums\Models\List\PlaylistVisibility;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\Pivot\PivotController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Resources\Pivot\List\Collection\PlaylistImageCollection;
use App\Http\Resources\Pivot\List\Resource\PlaylistImageResource;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use App\Pivots\List\PlaylistImage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * Class PlaylistImageController.
 */
class PlaylistImageController extends PivotController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Playlist::class, 'playlist', Image::class, 'image');

        $isPlaylistManagementAllowed = Str::of('is_feature_enabled:')
            ->append(FlagConstants::ALLOW_PLAYLIST_MANAGEMENT_QUALIFIED)
            ->append(',Playlist Management Disabled')
            ->__toString();

        $this->middleware($isPlaylistManagementAllowed)->except(['index', 'show']);
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

        $builder = PlaylistImage::query()
            ->whereHas(PlaylistImage::RELATION_PLAYLIST, function (Builder $relationBuilder) {
                $relationBuilder->where(Playlist::ATTRIBUTE_VISIBILITY, PlaylistVisibility::PUBLIC);
            });

        $resources = $action->index($builder, $query, $request->schema());

        $collection = new PlaylistImageCollection($resources, $query);

        return $collection->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  Playlist  $playlist
     * @param  Image  $image
     * @param  StoreAction  $action
     * @return JsonResponse
     */
    public function store(StoreRequest $request, Playlist $playlist, Image $image, StoreAction $action): JsonResponse
    {
        $validated = array_merge(
            $request->validated(),
            [
                PlaylistImage::ATTRIBUTE_IMAGE => $image->getKey(),
                PlaylistImage::ATTRIBUTE_PLAYLIST => $playlist->getKey(),
            ]
        );

        $playlistImage = $action->store(PlaylistImage::query(), $validated);

        $resource = new PlaylistImageResource($playlistImage, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Playlist  $playlist
     * @param  Image  $image
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, Playlist $playlist, Image $image, ShowAction $action): JsonResponse
    {
        $playlistImage = PlaylistImage::query()
            ->where(PlaylistImage::ATTRIBUTE_PLAYLIST, $playlist->getKey())
            ->where(PlaylistImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($playlistImage, $query, $request->schema());

        $resource = new PlaylistImageResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Playlist  $playlist
     * @param  Image  $image
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Playlist $playlist, Image $image, DestroyAction $action): JsonResponse
    {
        $playlistImage = PlaylistImage::query()
            ->where(PlaylistImage::ATTRIBUTE_PLAYLIST, $playlist->getKey())
            ->where(PlaylistImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->firstOrFail();

        $action->destroy($playlistImage);

        return new JsonResponse([
            'message' => "Image '{$image->getName()}' has been detached from Playlist '{$playlist->getName()}'.",
        ]);
    }
}
