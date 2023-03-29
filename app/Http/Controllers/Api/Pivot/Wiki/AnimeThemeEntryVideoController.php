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
use App\Http\Resources\Pivot\Wiki\Collection\AnimeThemeEntryVideoCollection;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeThemeEntryVideoResource;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Http\JsonResponse;

/**
 * Class AnimeThemeEntryVideoController.
 */
class AnimeThemeEntryVideoController extends PivotController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeThemeEntry::class, 'animethemeentry', Video::class, 'video');
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

        $resources = $action->index(AnimeThemeEntryVideo::query(), $query, $request->schema());

        $collection = new AnimeThemeEntryVideoCollection($resources, $query);

        return $collection->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreRequest  $request
     * @param  AnimeThemeEntry  $animethemeentry
     * @param  Video  $video
     * @param  StoreAction  $action
     * @return JsonResponse
     */
    public function store(StoreRequest $request, AnimeThemeEntry $animethemeentry, Video $video, StoreAction $action): JsonResponse
    {
        $validated = array_merge(
            $request->validated(),
            [
                AnimeThemeEntryVideo::ATTRIBUTE_ENTRY => $animethemeentry->getKey(),
                AnimeThemeEntryVideo::ATTRIBUTE_VIDEO => $video->getKey(),
            ]
        );

        $entryVideo = $action->store(AnimeThemeEntryVideo::query(), $validated);

        $resource = new AnimeThemeEntryVideoResource($entryVideo, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  AnimeThemeEntry  $animethemeentry
     * @param  Video  $video
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, AnimeThemeEntry $animethemeentry, Video $video, ShowAction $action): JsonResponse
    {
        $entryVideo = AnimeThemeEntryVideo::query()
            ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, $animethemeentry->getKey())
            ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, $video->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($entryVideo, $query, $request->schema());

        $resource = new AnimeThemeEntryVideoResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  AnimeThemeEntry  $animethemeentry
     * @param  Video  $video
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(AnimeThemeEntry $animethemeentry, Video $video, DestroyAction $action): JsonResponse
    {
        $entryVideo = AnimeThemeEntryVideo::query()
            ->where(AnimeThemeEntryVideo::ATTRIBUTE_ENTRY, $animethemeentry->getKey())
            ->where(AnimeThemeEntryVideo::ATTRIBUTE_VIDEO, $video->getKey())
            ->firstOrFail();

        $action->destroy($entryVideo);

        return new JsonResponse([
            'message' => "Video '{$video->getName()}' has been detached from Entry '{$animethemeentry->getName()}'.",
        ]);
    }
}
