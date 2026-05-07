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
            'id' => (string) $video->getKey(),
            'filename' => $video->filename,
            'tags' => $video->tags,
            'created_at' => $video->created_at?->timestamp,
            'entries' => $video->animethemeentries->map(
                fn (AnimeThemeEntry $entry): array => $entry->toSearchableArray()
            )->all(),
        ];
    }
}
