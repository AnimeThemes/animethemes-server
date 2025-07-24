<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Pivot\Wiki;

use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Pivot\Wiki\AnimeImage\AnimeImageAnimeIdField;
use App\Http\Api\Field\Pivot\Wiki\AnimeImage\AnimeImageImageIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeImageResource;
use App\Pivots\Wiki\AnimeImage;

class AnimeImageSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     */
    public function type(): string
    {
        return AnimeImageResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new AnimeSchema(), AnimeImage::RELATION_ANIME),
            new AllowedInclude(new ImageSchema(), AnimeImage::RELATION_IMAGE),
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
            new AnimeImageAnimeIdField($this),
            new AnimeImageImageIdField($this),
        ];
    }
}
