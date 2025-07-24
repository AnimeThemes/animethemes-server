<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Pivot\List;

use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Pivot\List\PlaylistImage\PlaylistImageImageIdField;
use App\Http\Api\Field\Pivot\List\PlaylistImage\PlaylistImagePlaylistIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Api\Schema\Wiki\ImageSchema;
use App\Http\Resources\Pivot\List\Resource\PlaylistImageResource;
use App\Pivots\List\PlaylistImage;

class PlaylistImageSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return PlaylistImageResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    public function allowedIncludes(): array
    {
        return $this->withIntermediatePaths([
            new AllowedInclude(new PlaylistSchema(), PlaylistImage::RELATION_PLAYLIST),
            new AllowedInclude(new ImageSchema(), PlaylistImage::RELATION_IMAGE),
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
            new PlaylistImagePlaylistIdField($this),
            new PlaylistImageImageIdField($this),
        ];
    }
}
