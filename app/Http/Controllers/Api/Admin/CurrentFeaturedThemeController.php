<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Http\Api\ShowAction;
use App\Contracts\Http\Api\InteractsWithSchema;
use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\FeaturedThemeSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\Admin\Resource\FeaturedThemeResource;
use App\Models\Admin\FeaturedTheme;
use Illuminate\Support\Facades\Date;

class CurrentFeaturedThemeController extends Controller implements InteractsWithSchema
{
    /**
     * Display the specified resource.
     *
     * @param  ShowRequest  $request
     * @param  ShowAction  $action
     * @return FeaturedThemeResource
     */
    public function show(ShowRequest $request, ShowAction $action): FeaturedThemeResource
    {
        $query = new Query($request->validated());

        $featuredtheme = FeaturedTheme::query()
            ->whereNotNull(FeaturedTheme::ATTRIBUTE_START_AT)
            ->whereNotNull(FeaturedTheme::ATTRIBUTE_END_AT)
            ->whereDate(FeaturedTheme::ATTRIBUTE_START_AT, ComparisonOperator::LTE->value, Date::now())
            ->whereDate(FeaturedTheme::ATTRIBUTE_END_AT, ComparisonOperator::GT->value, Date::now())
            ->firstOrFail();

        $show = $action->show($featuredtheme, $query, $request->schema());

        return new FeaturedThemeResource($show, $query);
    }

    /**
     * Get the underlying schema.
     */
    public function schema(): Schema
    {
        return new FeaturedThemeSchema();
    }
}
