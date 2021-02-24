<?php

namespace Tests\Feature\Http\Api;

use App\Models\Song;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SongTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Get attributes for Song resource.
     *
     * @param Song $song
     * @return array
     */
    public static function getData(Song $song)
    {
        return [
            'id' => $song->song_id,
            'title' => strval($song->title),
            'created_at' => $song->created_at->toJSON(),
            'updated_at' => $song->updated_at->toJSON(),
        ];
    }
}
