<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Models\List;

use App\Models\List\Playlist;

class PlaylistElasticModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Playlist $playlist): array
    {
        return $playlist->attributesToArray();
    }
}
