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
            'id' => (string) $series->getKey(),
            'name' => $series->name,
            'created_at' => $series->created_at?->timestamp,
            'updated_at' => $series->updated_at?->timestamp,
            'anime' => $series->anime->map(
                fn (Anime $anime): array => $anime->toSearchableArray()
            )->all(),
        ];
    }
}
