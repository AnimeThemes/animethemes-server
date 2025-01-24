<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Pivot\Wiki;

use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Pivot\Wiki\AnimeStudio\AnimeStudioAnimeIdField;
use App\Http\Api\Field\Pivot\Wiki\AnimeStudio\AnimeStudioStudioIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\AnimeSchema;
use App\Http\Api\Schema\Wiki\StudioSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeStudioResource;
use App\Pivots\Wiki\AnimeStudio;

/**
 * Class AnimeStudioSchema.
 */
class AnimeStudioSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return AnimeStudioResource::$wrap;
    }

    /**
     * Get the allowed includes.
     *
     * @return AllowedInclude[]
     */
    protected function finalAllowedIncludes(): array
    {
        return [
            new AllowedInclude(new AnimeSchema(), AnimeStudio::RELATION_ANIME),
            new AllowedInclude(new StudioSchema(), AnimeStudio::RELATION_STUDIO),
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
            new AnimeStudioAnimeIdField($this),
            new AnimeStudioStudioIdField($this),
        ];
    }
}
