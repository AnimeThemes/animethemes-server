<?php

namespace Tests\Feature\Http\Api;

use App\Models\Anime;
use Tests\TestCase;

class AnimeTest extends TestCase
{
    /**
     * Get attributes for Anime resource.
     *
     * @param Anime $anime
     * @return array
     */
    public static function getData(Anime $anime)
    {
        return [
            'id' => $anime->anime_id,
            'name' => $anime->name,
            'slug' => $anime->slug,
            'year' => $anime->year,
            'season' => strval(optional($anime->season)->description),
            'synopsis' => $anime->synopsis,
            'created_at' => $anime->created_at->toJSON(),
            'updated_at' => $anime->updated_at->toJSON(),
        ];
    }
}
