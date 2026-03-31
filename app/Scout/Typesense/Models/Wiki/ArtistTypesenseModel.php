<?php

declare(strict_types=1);

namespace App\Scout\Typesense\Models\Wiki;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song\Membership;
use App\Models\Wiki\Song\Performance;
use App\Models\Wiki\Synonym;

class ArtistTypesenseModel
{
    /**
     * @return array<string, mixed>
     */
    public static function toSearchableArray(Artist $artist): array
    {
        return [
            'id' => (string) $artist->getKey(),
            'name' => $artist->name,
            'created_at' => $artist->created_at?->timestamp,
            'updated_at' => $artist->updated_at?->timestamp,
            'deleted_at' => $artist->deleted_at?->timestamp,
            'synonyms' => $synonyms = $artist->synonyms->map(fn (Synonym $synonym) => $synonym->text)->all(),
            'as' => $as = $artist->performances->map(fn (Performance $performance) => $performance->as)
                ->toBase()
                ->concat($artist->memberships->map(fn (Membership $membership) => $membership->as))
                ->filter()
                ->unique()
                ->values()
                ->all(),
            'search_text' => implode(' ', [
                $artist->name,
                ...$synonyms,
                // ...$as,
            ]),
        ];
    }
}
