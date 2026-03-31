<?php

declare(strict_types=1);

namespace App\Scout\Typesense\Models\Wiki;

use App\Models\Wiki\Studio;

class StudioTypesenseModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Studio $studio): array
    {
        return [
            ...$studio->attributesToArray(),
            'id' => (string) $studio->getKey(),
            'created_at' => $studio->created_at?->timestamp,
        ];
    }
}
