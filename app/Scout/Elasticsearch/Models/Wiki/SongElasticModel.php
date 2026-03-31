<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Models\Wiki;

use App\Models\Wiki\Song;

class SongElasticModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Song $song): array
    {
        return $song->attributesToArray();
    }
}
