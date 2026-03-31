<?php

declare(strict_types=1);

namespace App\Scout\Typesense\Models\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Series;

class SeriesTypesenseModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Series $series): array
    {
        return [
            ...$series->attributesToArray(),
            'id' => (string) $series->getKey(),
            'created_at' => $series->created_at?->timestamp,
            'anime' => $series->anime->map(
                fn (Anime $anime): array => $anime->toSearchableArray()
            )->all(),
        ];
    }
}
