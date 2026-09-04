<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Models\Wiki;

use App\Models\Wiki\Entry;
use Illuminate\Support\Str;

class EntryElasticModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Entry $entry): array
    {
        return [
            ...$entry->attributesToArray(),
            'theme' => $entry->animetheme->toSearchableArray(),
            'version' => Str::of(strval($entry->version))->prepend('v')->__toString(),
        ];
    }
}
