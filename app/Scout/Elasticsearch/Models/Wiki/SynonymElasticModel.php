<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Models\Wiki;

use App\Models\Wiki\Synonym;

class SynonymElasticModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Synonym $synonym): array
    {
        return $synonym->attributesToArray();
    }
}
