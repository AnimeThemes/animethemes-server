<?php

declare(strict_types=1);

namespace App\Scout\Typesense\Models\Wiki;

use App\Models\Wiki\Entry;
use Illuminate\Support\Str;

class EntryTypesenseModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Entry $entry): array
    {
        return [
            'id' => (string) $entry->getKey(),
            'animetheme' => $entry->animetheme->toSearchableArray(),
            'version' => $version = Str::of(strval($entry->version))->prepend('v')->__toString(),
            'type_sequence_version' => $entry->animetheme->type->localize().(($entry->animetheme->sequence ?? 1)).$version,
        ];
    }
}
