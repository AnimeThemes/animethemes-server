<?php

declare(strict_types=1);

namespace App\Scout\Typesense\Models\Wiki\Anime;

use App\Models\Wiki\Anime\AnimeSynonym;

class AnimeSynonymTypesenseModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(AnimeSynonym $synonym): array
    {
        return [
            ...$synonym->attributesToArray(),
            'id' => (string) $synonym->getKey(),
            'created_at' => $synonym->created_at?->timestamp,
            'updated_at' => $synonym->updated_at?->timestamp,
            'deleted_at' => $synonym->deleted_at?->timestamp,
        ];
    }
}
