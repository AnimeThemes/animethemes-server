<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Models\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Synonym;

class AnimeElasticModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Anime $anime): array
    {
        return [
            ...$anime->attributesToArray(),
            'synonyms' => $anime->synonyms->map(fn (Synonym $synonym) => $synonym->text)->all(),
        ];
    }
}
