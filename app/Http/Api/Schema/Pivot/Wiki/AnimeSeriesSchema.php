<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Pivot\Wiki;

use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Pivot\Wiki\AnimeSeries\AnimeSeriesAnimeIdField;
use App\Http\Api\Field\Pivot\Wiki\AnimeSeries\AnimeSeriesSeriesIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Api\Schema\Wiki\SeriesSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeSeriesResource;
use App\Pivots\Wiki\AnimeSeries;

/**
 * Class AnimeSeriesSchema.
 */
class AnimeSeriesSchema extends EloquentSchema
{
    /**
     * The model this schema represents.
     *
     * @return string
     */
    public function model(): string
    {
        return AnimeSeries::class;
    }

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return AnimeSeriesResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            new AllowedInclude(new AnimeSchema(), AnimeSeries::RELATION_ANIME),
            new AllowedInclude(new SeriesSchema(), AnimeSeries::RELATION_SERIES),
        ];
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function fields(): array
    {
        return [
            new CreatedAtField($this),
            new UpdatedAtField($this),
            new AnimeSeriesAnimeIdField($this),
            new AnimeSeriesSeriesIdField($this),
        ];
    }
}
