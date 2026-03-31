<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Models\Wiki;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;

class VideoElasticModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Video $video): array
    {
        return [
            ...$video->attributesToArray(),
            'entries' => $video->animethemeentries->map(
                fn (AnimeThemeEntry $entry): array => $entry->toSearchableArray()
            )->all(),
        ];
    }
}
