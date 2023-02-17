<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Anime;

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
use App\Http\Resources\Wiki\Anime\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Anime\Resource\ThemeResource;
use App\Models\Wiki\Anime\AnimeTheme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class ThemeController.
 */
class ThemeController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeTheme::class, 'animetheme');
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
            : $action->index(AnimeTheme::query(), $query, $request->schema());

        $collection = new ThemeCollection($videos, $query);

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
        $theme = $action->store(AnimeTheme::query(), $request->validated());

        $resource = new ThemeResource($theme, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  AnimeTheme  $animetheme
     * @param  ShowAction  $action
     * @return JsonResponse
     */
    public function show(ShowRequest $request, AnimeTheme $animetheme, ShowAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        $show = $action->show($animetheme, $query, $request->schema());

        $resource = new ThemeResource($show, $query);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  AnimeTheme  $animetheme
     * @param  UpdateAction  $action
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, AnimeTheme $animetheme, UpdateAction $action): JsonResponse
    {
        $updated = $action->update($animetheme, $request->validated());

        $resource = new ThemeResource($updated, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  Request  $request
     * @param  AnimeTheme  $animetheme
     * @param  DestroyAction  $action
     * @return JsonResponse
     */
    public function destroy(Request $request, AnimeTheme $animetheme, DestroyAction $action): JsonResponse
    {
        $deleted = $action->destroy($animetheme);

        $resource = new ThemeResource($deleted, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  Request  $request
     * @param  AnimeTheme  $animetheme
     * @param  RestoreAction  $action
     * @return JsonResponse
     */
    public function restore(Request $request, AnimeTheme $animetheme, RestoreAction $action): JsonResponse
    {
        $restored = $action->restore($animetheme);

        $resource = new ThemeResource($restored, new Query());

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  AnimeTheme  $animetheme
     * @param  ForceDeleteAction  $action
     * @return JsonResponse
     */
    public function forceDelete(AnimeTheme $animetheme, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($animetheme);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
