<?php

namespace Tests\Feature\Api;

use App\Models\Anime;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testShowVideoAttributes()
    {
        $video = Video::factory()->create();

        $response = $this->get(route('api.video.show', ['video' => $video]));

        $response->assertJson(static::getData($video));
    }

    public function testShowVideoEntriesAttributes()
    {
        $video = Video::factory()
            ->has(Entry::factory()->for(Theme::factory()->for(Anime::factory()))->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.video.show', ['video' => $video]));

        $response->assertJson([
            'entries' => $video->entries->map(function($entry) {
                return EntryTest::getData($entry);
            })->toArray()
        ]);
    }

    public static function getData(Video $video) {
        return [
            'id' => $video->video_id,
            'basename' => $video->basename,
            'filename' => $video->filename,
            'path' => $video->path,
            'resolution' => $video->resolution,
            'nc' => $video->nc,
            'subbed' => $video->subbed,
            'lyrics' => $video->lyrics,
            'uncen' => $video->uncen,
            'source' => strval(optional($video->source)->description),
            'overlap' => strval(optional($video->overlap)->description),
            'created_at' => $video->created_at->toJSON(),
            'updated_at' => $video->updated_at->toJSON(),
            'link' => route('video.show', $video),
        ];
    }
}
