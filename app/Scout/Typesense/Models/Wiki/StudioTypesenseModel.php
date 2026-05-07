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
            'id' => (string) $studio->getKey(),
            'name' => $studio->name,
            'created_at' => $studio->created_at?->timestamp,
            'updated_at' => $studio->updated_at?->timestamp,
        ];
    }
}
