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
            'id' => (string) $synonym->getKey(),
            'text' => $synonym->text,
        ];
    }
}
