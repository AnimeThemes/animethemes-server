<?php

declare(strict_types=1);

namespace App\Scout\Typesense\Models\List;

use App\Models\List\Playlist;

class PlaylistTypesenseModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Playlist $playlist): array
    {
        return [
            'id' => (string) $playlist->getKey(),
            'name' => $playlist->name,
            'created_at' => $playlist->created_at?->timestamp,
        ];
    }
}
