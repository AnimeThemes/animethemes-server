<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Models\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;

class SeriesElasticModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Series $series): array
    {
        return [
            ...$series->attributesToArray(),
            'anime' => $series->anime->map(
                fn (Anime $anime): array => $anime->toSearchableArray()
            )->all(),
        ];
    }
}
