<?php

namespace Tests\Feature\Http\Api;

use App\Models\Entry;
use Tests\TestCase;

class EntryTest extends TestCase
{
    /**
     * Get attributes for Entry resource.
     *
     * @param Entry $entry
     * @return array
     */
    public static function getData(Entry $entry)
    {
        return [
            'id' => $entry->entry_id,
            'version' => is_null($entry->version) ? '' : $entry->version,
            'episodes' => strval($entry->episodes),
            'nsfw' => $entry->nsfw,
            'spoiler' => $entry->spoiler,
            'notes' => strval($entry->notes),
            'created_at' => $entry->created_at->toJSON(),
            'updated_at' => $entry->updated_at->toJSON(),
        ];
    }
}
