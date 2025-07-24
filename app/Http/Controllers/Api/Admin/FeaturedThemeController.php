<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\Admin\Collection\FeaturedThemeCollection;
use App\Http\Resources\Admin\Resource\FeaturedThemeResource;
use App\Models\Admin\FeaturedTheme;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;

class FeaturedThemeController extends BaseController
{
    public function __construct()
    {
        parent::__construct(FeaturedTheme::class, 'featuredtheme');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexAction  $action
     */
    public function index(IndexRequest $request, IndexAction $action): FeaturedThemeCollection
    {
        $query = new Query($request->validated());

        $builder = FeaturedTheme::query()
            ->whereNotNull(FeaturedTheme::ATTRIBUTE_START_AT)
            ->whereDate(FeaturedTheme::ATTRIBUTE_START_AT, ComparisonOperator::LTE->value, Date::now());

        $featuredthemes = $action->index($builder, $query, $request->schema());

        return new FeaturedThemeCollection($featuredthemes, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreAction<FeaturedTheme>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): FeaturedThemeResource
    {
        $featuredtheme = $action->store(FeaturedTheme::query(), $request->validated());

        return new FeaturedThemeResource($featuredtheme, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowAction  $action
     */
    public function show(ShowRequest $request, FeaturedTheme $featuredtheme, ShowAction $action): FeaturedThemeResource
    {
        $query = new Query($request->validated());

        $show = $action->show($featuredtheme, $query, $request->schema());

        return new FeaturedThemeResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateAction  $action
     */
    public function update(UpdateRequest $request, FeaturedTheme $featuredtheme, UpdateAction $action): FeaturedThemeResource
    {
        $updated = $action->update($featuredtheme, $request->validated());

        return new FeaturedThemeResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  DestroyAction  $action
     */
    public function destroy(FeaturedTheme $featuredtheme, DestroyAction $action): JsonResponse
    {
        $message = $action->forceDelete($featuredtheme);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
