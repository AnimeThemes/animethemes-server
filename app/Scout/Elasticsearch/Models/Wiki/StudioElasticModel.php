<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Models\Wiki;

use App\Models\Wiki\Studio;

class StudioElasticModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Studio $studio): array
    {
        return $studio->attributesToArray();
    }
}
