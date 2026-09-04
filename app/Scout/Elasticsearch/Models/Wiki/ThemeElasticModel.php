<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Models\Wiki;

use App\Models\Wiki\Theme;

class ThemeElasticModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Theme $theme): array
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
