<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Actions\Http\Api\ShowAction;
use App\Contracts\Http\Api\InteractsWithSchema;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Admin\FeaturedThemeSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\Admin\Resource\FeaturedThemeJsonResource;
use App\Models\Admin\FeaturedTheme;
use Illuminate\Support\Facades\Date;

class CurrentFeaturedThemeController extends Controller implements InteractsWithSchema
{
    public function show(ShowRequest $request, ShowAction $action): FeaturedThemeJsonResource
    {
        $query = new Query($request->validated());

        $featuredtheme = FeaturedTheme::query()
            ->whereValueBetween(Date::now(), [
                FeaturedTheme::ATTRIBUTE_START_AT,
                FeaturedTheme::ATTRIBUTE_END_AT,
            ])
            ->firstOrFail();

        $show = $action->show($featuredtheme, $query, $request->schema());

        return new FeaturedThemeJsonResource($show, $query);
    }

    /**
     * Get the underlying schema.
     */
    public function schema(): Schema
    {
        return new FeaturedThemeSchema();
    }
}
