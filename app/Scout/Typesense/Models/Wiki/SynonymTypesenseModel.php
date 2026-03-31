<?php

declare(strict_types=1);

namespace App\Scout\Typesense\Models\Wiki;

use App\Models\Wiki\Synonym;

class SynonymTypesenseModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Synonym $synonym): array
    {
        return [
            ...$synonym->attributesToArray(),
            'id' => (string) $synonym->getKey(),
            'created_at' => $synonym->created_at?->timestamp,
        ];
    }
}
