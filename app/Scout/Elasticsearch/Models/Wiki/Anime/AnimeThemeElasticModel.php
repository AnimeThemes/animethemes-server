<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Models\Wiki\Anime;

use App\Models\Wiki\Anime\AnimeTheme;

class AnimeThemeElasticModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(AnimeTheme $theme): array
    {
        $array = [
            ...$theme->attributesToArray(),
            'anime' => $theme->anime->attributesToArray(),
        ];

        if ($theme->song !== null) {
            $array['song'] = $theme->song->toSearchableArray();
        }

        return $array;
    }
}
