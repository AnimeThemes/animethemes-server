<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Models\Wiki;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Performance;
use App\Models\Wiki\Synonym;

class ArtistElasticModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Artist $artist): array
    {
        return [
            ...$artist->attributesToArray(),
            'synonyms' => $artist->synonyms->map(fn (Synonym $synonym) => $synonym->text)->all(),
            'as' => $artist->performances->map(fn (Performance $performance) => $performance->as)
                ->toBase()
                ->concat($artist->memberPerformances->map(fn (Performance $performance) => $performance->member_as))
                ->filter()
                ->unique()
                ->values()
                ->all(),
        ];
    }
}
