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
use App\Http\Resources\Wiki\Collection\ThemeCollection;
use App\Http\Resources\Wiki\Resource\ThemeJsonResource;
use App\Models\Wiki\Theme;
use Illuminate\Http\JsonResponse;

class ThemeController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Theme::class, 'animetheme');
    }

    public function index(IndexRequest $request, IndexAction $action): ThemeCollection
    {
        $query = new Query($request->validated());

        $themes = $query->hasSearchCriteria()
            ? $action->search($query, $request->schema())
            : $action->index(Theme::query(), $query, $request->schema());

        return new ThemeCollection($themes, $query);
    }

    /**
     * @param  StoreAction<Theme>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): ThemeJsonResource
    {
        $theme = $action->store(Theme::query(), $request->validated());

        return new ThemeJsonResource($theme, new Query());
    }

    public function show(ShowRequest $request, Theme $animetheme, ShowAction $action): ThemeJsonResource
    {
        $query = new Query($request->validated());

        $show = $action->show($animetheme, $query, $request->schema());

        return new ThemeJsonResource($show, $query);
    }

    public function update(UpdateRequest $request, Theme $animetheme, UpdateAction $action): ThemeJsonResource
    {
        $updated = $action->update($animetheme, $request->validated());

        return new ThemeJsonResource($updated, new Query());
    }

    public function destroy(Theme $animetheme, DestroyAction $action): ThemeJsonResource
    {
        $deleted = $action->destroy($animetheme);

        return new ThemeJsonResource($deleted, new Query());
    }

    public function restore(Theme $animetheme, RestoreAction $action): ThemeJsonResource
    {
        $restored = $action->restore($animetheme);

        return new ThemeJsonResource($restored, new Query());
    }

    public function forceDelete(Theme $animetheme, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($animetheme);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
