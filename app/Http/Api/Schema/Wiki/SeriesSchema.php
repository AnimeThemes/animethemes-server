<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Wiki;

use App\Http\Api\Field\Field;
use App\Http\Api\Field\IntField;
use App\Http\Api\Field\StringField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\SeriesResource;
use App\Models\Wiki\Series;

/**
 * Class SeriesSchema.
 */
class SeriesSchema extends Schema
{
    /**
     * The model this schema represents.
     *
     * @var string|null
     */
    public static ?string $model = Series::class;

    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return SeriesResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return [
            AllowedInclude::make(AnimeSchema::class, Series::RELATION_ANIME),
        ];
    }

    /**
     * Get the direct fields of the resource.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return array_merge(
            parent::fields(),
            [
                new IntField(BaseResource::ATTRIBUTE_ID, Series::ATTRIBUTE_ID),
                new StringField(Series::ATTRIBUTE_NAME),
                new StringField(Series::ATTRIBUTE_SLUG),
            ],
        );
    }
}
