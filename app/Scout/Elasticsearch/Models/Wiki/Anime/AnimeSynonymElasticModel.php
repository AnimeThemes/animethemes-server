<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Models\Wiki\Anime;

use App\Models\Wiki\Anime\AnimeSynonym;

class AnimeSynonymElasticModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(AnimeSynonym $synonym): array
    {
        return $synonym->attributesToArray();
    }
}
