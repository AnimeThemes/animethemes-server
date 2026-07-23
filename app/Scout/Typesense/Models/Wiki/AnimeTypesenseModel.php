<?php

declare(strict_types=1);

namespace App\Scout\Typesense\Models\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Synonym;

class AnimeTypesenseModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Anime $anime): array
    {
        return [
            'id' => (string) $anime->getKey(),
            'format' => $anime->format?->value,
            'title' => $anime->title,
            // So TypeSense does not boost when alternative titles are the same.
            'title_english' => $anime->title_english !== $anime->title
                ? $anime->title_english
                : null,
            'title_native' => $anime->title_native !== $anime->title
                ? $anime->title_native
                : null,
            'season' => $anime->season?->value,
            'year' => $anime->year,
            'created_at' => $anime->created_at?->timestamp,
            'updated_at' => $anime->updated_at?->timestamp,
            'synonyms' => $anime->synonyms->map(fn (Synonym $synonym) => $synonym->text)->all(),
        ];
    }
}
