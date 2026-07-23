<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Wiki\Song;

class SongType
{
    /**
     * @return array<string, string>
     */
    public function resolveTitleAttribute(Song $song): array
    {
        return [
            'romaji' => $song->title,
            'native' => $song->title_native,
        ];
    }
}
