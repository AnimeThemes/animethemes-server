<?php

declare(strict_types=1);

namespace App\Scout\Typesense\Models\List;

use App\Models\List\ExternalProfile;

class ExternalProfileTypesenseModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(ExternalProfile $profile): array
    {
        return [
            ...$profile->attributesToArray(),
            'id' => (string) $profile->getKey(),
        ];
    }
}
