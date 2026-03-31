<?php

declare(strict_types=1);

namespace App\Scout\Typesense\Models\Wiki;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

class VideoTypesenseModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Video $video): array
    {
        return [
            ...$video->attributesToArray(),
            'id' => (string) $video->getKey(),
            'created_at' => $video->created_at?->timestamp,
            'updated_at' => $video->updated_at?->timestamp,
            'deleted_at' => $video->deleted_at?->timestamp,
            'entries' => $video->animethemeentries->map(
                fn (AnimeThemeEntry $entry): array => $entry->toSearchableArray()
            )->all(),
        ];
    }
}
