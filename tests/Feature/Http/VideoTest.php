<?php

namespace Tests\Feature\Http;

use App\Models\Video;
use GuzzleHttp\Psr7\MimeType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use RefreshDatabase, WithFaker, WithoutEvents;

    /**
     * If video streaming is disabled through the 'app.allow_video_streams' property,
     * the user shall be redirected to the Welcome Screen.
     *
     * @return void
     */
    public function testVideoStreamingNotAllowedRedirect()
    {
        Config::set('app.allow_video_streams', false);

        $video = Video::factory()->create();

        $response = $this->get(route('video.show', ['video' => $video]));

        $response->assertRedirect(route('welcome'));
    }

    /**
     * If the video is soft deleted, the user shall be redirected to the Welcome Screen.
     *
     * @return void
     */
    public function testSoftDeleteVideoStreamingRedirect()
    {
        Config::set('app.allow_video_streams', true);

        $video = Video::factory()->create();

        $video->delete();

        $response = $this->get(route('video.show', ['video' => $video]));

        $response->assertRedirect(route('welcome'));
    }

    /**
     * If video streaming is enabled, the video show route shall stream the video.
     *
     * @return void
     */
    public function testVideoStreaming()
    {
        Config::set('app.allow_video_streams', true);

        $fs = Storage::fake('videos');
        $file = File::fake()->create($this->faker->word().'.webm');
        $fsFile = $fs->put('', $file);
        $fsPathinfo = pathinfo(strval($fsFile));

        $video = Video::create([
            'basename' => $fsPathinfo['basename'],
            'filename' => $fsPathinfo['filename'],
            'path' => $this->faker->word(),
            'size' => $this->faker->randomNumber(),
            'mimetype' => MimeType::fromFilename($fsPathinfo['basename']),
        ]);

        $response = $this->get(route('video.show', ['video' => $video]));

        $this->assertInstanceOf(StreamedResponse::class, $response->baseResponse);
    }
}
