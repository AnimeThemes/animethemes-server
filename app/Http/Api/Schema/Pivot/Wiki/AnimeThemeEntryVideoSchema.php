<?php

declare(strict_types=1);

namespace App\Http\Api\Schema\Pivot\Wiki;

use App\Http\Api\Field\Base\CreatedAtField;
use App\Http\Api\Field\Base\UpdatedAtField;
use App\Http\Api\Field\Field;
use App\Http\Api\Field\Pivot\Wiki\AnimeThemeEntryVideo\AnimeThemeEntryVideoEntryIdField;
use App\Http\Api\Field\Pivot\Wiki\AnimeThemeEntryVideo\AnimeThemeEntryVideoVideoIdField;
use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\EloquentSchema;
use App\Http\Api\Schema\Wiki\Anime\Theme\EntrySchema;
use App\Http\Api\Schema\Wiki\VideoSchema;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeThemeEntryVideoResource;
use App\Pivots\Wiki\AnimeThemeEntryVideo;

/**
 * Class AnimeThemeEntryVideoSchema.
 */
class AnimeThemeEntryVideoSchema extends EloquentSchema
{
    /**
     * Get the type of the resource.
     *
     * @return string
     */
    public function type(): string
    {
        return AnimeThemeEntryVideoResource::$wrap;
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
                new AllowedInclude(new EntrySchema(), AnimeThemeEntryVideo::RELATION_ENTRY),
                new AllowedInclude(new VideoSchema(), AnimeThemeEntryVideo::RELATION_VIDEO),
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
            new AnimeThemeEntryVideoEntryIdField($this),
            new AnimeThemeEntryVideoVideoIdField($this),
        ];
    }
}
