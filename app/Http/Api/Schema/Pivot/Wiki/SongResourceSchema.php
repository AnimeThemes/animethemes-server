<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Pivot\Wiki;

use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Pivot\Wiki\SongResource\SongResourceSongIdField;
use App\Http\Api\Field\Pivot\Wiki\SongResource\SongResourceAsField;
use App\Http\Api\Field\Pivot\Wiki\SongResource\SongResourceResourceIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\ExternalResourceSchema;
use App\Http\Api\Schema\Wiki\SongSchema;
use App\Http\Resources\Pivot\Wiki\Resource\SongResourceResource;
use App\Pivots\Wiki\SongResource;

/**
 * Class SongResourceSchema.
 */
class SongResourceSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return SongResourceResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return array_merge(
            $this->withIntermediatePaths([
                new AllowedInclude(new SongSchema(), SongResource::RELATION_SONG),
                new AllowedInclude(new ExternalResourceSchema(), SongResource::RELATION_RESOURCE),
            ]),
            []
        );
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
            new SongResourceSongIdField($this),
            new SongResourceResourceIdField($this),
            new SongResourceAsField($this),
        ];
    }
}
