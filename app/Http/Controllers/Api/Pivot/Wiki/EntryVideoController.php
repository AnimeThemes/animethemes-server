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
use App\Http\Resources\Pivot\Wiki\Collection\EntryVideoCollection;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeThemeEntryVideoJsonResource;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\EntryVideo;
use Illuminate\Http\JsonResponse;

class EntryVideoController extends PivotController
{
    public function __construct()
    {
        parent::__construct(Entry::class, 'animethemeentry', Video::class, 'video');
    }

    public function index(IndexRequest $request, IndexAction $action): EntryVideoCollection
    {
        $query = new Query($request->validated());

        $resources = $action->index(EntryVideo::query(), $query, $request->schema());

        return new EntryVideoCollection($resources, $query);
    }

    /**
     * @param  StoreAction<EntryVideo>  $action
     */
    public function store(StoreRequest $request, Entry $animethemeentry, Video $video, StoreAction $action): AnimeThemeEntryVideoJsonResource
    {
        $validated = array_merge(
            $request->validated(),
            [
                EntryVideo::ATTRIBUTE_ENTRY => $animethemeentry->getKey(),
                EntryVideo::ATTRIBUTE_VIDEO => $video->getKey(),
            ]
        );

        $entryVideo = $action->store(EntryVideo::query(), $validated);

        return new AnimeThemeEntryVideoJsonResource($entryVideo, new Query());
    }

    public function show(ShowRequest $request, Entry $animethemeentry, Video $video, ShowAction $action): AnimeThemeEntryVideoJsonResource
    {
        $entryVideo = EntryVideo::query()
            ->where(EntryVideo::ATTRIBUTE_ENTRY, $animethemeentry->getKey())
            ->where(EntryVideo::ATTRIBUTE_VIDEO, $video->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($entryVideo, $query, $request->schema());

        return new AnimeThemeEntryVideoJsonResource($show, $query);
    }

    public function destroy(Entry $animethemeentry, Video $video, DestroyAction $action): JsonResponse
    {
        $entryVideo = EntryVideo::query()
            ->where(EntryVideo::ATTRIBUTE_ENTRY, $animethemeentry->getKey())
            ->where(EntryVideo::ATTRIBUTE_VIDEO, $video->getKey())
            ->firstOrFail();

        $action->destroy($entryVideo);

        return new JsonResponse([
            'message' => "Video '{$video->getName()}' has been detached from Entry '{$animethemeentry->getName()}'.",
        ]);
    }
}
