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
            'theme' => $entry->theme->toSearchableArray(),
            'version' => $version = Str::of(strval($entry->version))->prepend('v')->__toString(),
            'type_sequence_version' => $entry->theme->type->localize().(($entry->theme->sequence ?? 1)).$version,
            'created_at' => $entry->created_at->timestamp,
        ];
    }
}
