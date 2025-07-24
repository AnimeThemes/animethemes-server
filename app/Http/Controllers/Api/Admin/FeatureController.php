<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\UpdateAction;
use App\Constants\FeatureConstants;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\Admin\Collection\FeatureCollection;
use App\Http\Resources\Admin\Resource\FeatureResource;
use App\Models\Admin\Feature;

class FeatureController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(Feature::class, 'feature');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return FeatureCollection
     */
    public function index(IndexRequest $request, IndexAction $action): FeatureCollection
    {
        $query = new Query($request->validated());

        $builder = Feature::query()->where(Feature::ATTRIBUTE_SCOPE, FeatureConstants::NULL_SCOPE);

        $features = $action->index($builder, $query, $request->schema());

        return new FeatureCollection($features, $query);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  Feature  $feature
     * @param  ShowAction  $action
     * @return FeatureResource
     */
    public function show(ShowRequest $request, Feature $feature, ShowAction $action): FeatureResource
    {
        $query = new Query($request->validated());

        $show = $action->show($feature, $query, $request->schema());

        return new FeatureResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateRequest  $request
     * @param  Feature  $feature
     * @param  UpdateAction  $action
     * @return FeatureResource
     */
    public function update(UpdateRequest $request, Feature $feature, UpdateAction $action): FeatureResource
    {
        $updated = $action->update($feature, $request->validated());

        return new FeatureResource($updated, new Query());
    }
}
