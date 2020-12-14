<?php

namespace Tests\Feature\Http\Api;

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

    /**
     * The Video Index Endpoint shall display the Video attributes.
     *
     * @return void
     */
    public function testVideoIndexAttributes()
    {
        $videos = Video::factory()
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.video.index'));

        $response->assertJson([
            'videos' => $videos->map(function ($video) {
                return static::getData($video);
            })->toArray(),
        ]);
    }

    /**
     * The Show Video Endpoint shall display the Video attributes.
     *
     * @return void
     */
    public function testShowVideoAttributes()
    {
        $video = Video::factory()->create();

        $response = $this->get(route('api.video.show', ['video' => $video]));

        $response->assertJson(static::getData($video));
    }

    /**
     * The Show Video Endpoint shall display the entries relation in an 'entries' attribute.
     *
     * @return void
     */
    public function testShowVideoEntriesAttributes()
    {
        $video = Video::factory()
            ->has(Entry::factory()->for(Theme::factory()->for(Anime::factory()))->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.video.show', ['video' => $video]));

        $response->assertJson([
            'entries' => $video->entries->map(function ($entry) {
                return EntryTest::getData($entry);
            })->toArray(),
        ]);
    }

    /**
     * Get attributes for Video resource.
     *
     * @param Video $video
     * @return array
     */
    public static function getData(Video $video)
    {
        return [
            'id' => $video->video_id,
            'basename' => $video->basename,
            'filename' => $video->filename,
            'path' => $video->path,
            'size' => $video->size,
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
