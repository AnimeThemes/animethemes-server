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
            'id' => (string) $synonym->getKey(),
            'text' => $synonym->text,
            'type' => $synonym->type->value,
        ];
    }
}
