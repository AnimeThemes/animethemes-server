<?php

declare(strict_types=1);

namespace App\Scout\Typesense\Models\Wiki;

use App\Models\Wiki\Song;

class SongTypesenseModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Song $song): array
    {
        return [
            ...$song->attributesToArray(),
            'id' => (string) $song->getKey(),
            'created_at' => $song->created_at?->timestamp,
            'updated_at' => $song->updated_at?->timestamp,
            'deleted_at' => $song->deleted_at?->timestamp,
        ];
    }
}
