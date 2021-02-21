<?php

namespace Tests\Feature\Http\Api;

use App\Models\Artist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArtistTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Get attributes for Artist resource.
     *
     * @param Artist $artist
     * @return array
     */
    public static function getData(Artist $artist)
    {
        return [
            'id' => $artist->artist_id,
            'name' => $artist->name,
            'slug' => $artist->slug,
            'created_at' => $artist->created_at->toJSON(),
            'updated_at' => $artist->updated_at->toJSON(),
        ];
    }
}
