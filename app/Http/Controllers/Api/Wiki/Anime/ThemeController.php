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

class ThemeController extends BaseController
{
    public function __construct()
    {
        parent::__construct(AnimeTheme::class, 'animetheme');
    }

    public function index(IndexRequest $request, IndexAction $action): ThemeCollection
    {
        $query = new Query($request->validated());

        $themes = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(AnimeTheme::query(), $query, $request->schema());

        return new ThemeCollection($themes, $query);
    }

    /**
     * @param  StoreAction<AnimeTheme>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): ThemeResource
    {
        $theme = $action->store(AnimeTheme::query(), $request->validated());

        return new ThemeResource($theme, new Query());
    }

    public function show(ShowRequest $request, AnimeTheme $animetheme, ShowAction $action): ThemeResource
    {
        $query = new Query($request->validated());

        $show = $action->show($animetheme, $query, $request->schema());

        return new ThemeResource($show, $query);
    }

    public function update(UpdateRequest $request, AnimeTheme $animetheme, UpdateAction $action): ThemeResource
    {
        $updated = $action->update($animetheme, $request->validated());

        return new ThemeResource($updated, new Query());
    }

    public function destroy(AnimeTheme $animetheme, DestroyAction $action): ThemeResource
    {
        $deleted = $action->destroy($animetheme);

        return new ThemeResource($deleted, new Query());
    }

    public function restore(AnimeTheme $animetheme, RestoreAction $action): ThemeResource
    {
        $restored = $action->restore($animetheme);

        return new ThemeResource($restored, new Query());
    }

    public function forceDelete(AnimeTheme $animetheme, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($animetheme);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
