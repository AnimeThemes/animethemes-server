<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Models\List;

use App\Models\List\ExternalProfile;

class ExternalProfileElasticModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(ExternalProfile $profile): array
    {
        return $profile->attributesToArray();
    }
}
