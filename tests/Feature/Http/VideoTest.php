<?php

namespace Tests\Feature\Http;

use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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

        $response->assertRedirect(Config::get('app.url'));
    }
}
