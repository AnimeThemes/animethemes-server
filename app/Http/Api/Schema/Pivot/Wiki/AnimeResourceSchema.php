<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Pivot\Wiki;

use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Pivot\Wiki\AnimeResource\AnimeResourceAnimeIdField;
use App\Http\Api\Field\Pivot\Wiki\AnimeResource\AnimeResourceAsField;
use App\Http\Api\Field\Pivot\Wiki\AnimeResource\AnimeResourceResourceIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeResourceResource;
use App\Pivots\Wiki\AnimeResource;

class AnimeResourceSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return AnimeResourceResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), AnimeResource::RELATION_ANIME),
            new AllowedInclude(new ExternalResourceSchema(), AnimeResource::RELATION_RESOURCE),
        ]);
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
            new AnimeResourceAnimeIdField($this),
            new AnimeResourceResourceIdField($this),
            new AnimeResourceAsField($this),
        ];
    }
}
