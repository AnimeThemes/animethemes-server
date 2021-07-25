<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Wiki;

use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

/**
 * Class VideoTest.
 */
class VideoTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;
    use WithoutEvents;

    /**
     * If video streaming is disabled through the 'flags.allow_video_streams' property,
     * the user shall be redirected to the Welcome Screen.
     *
     * @return void
     */
    public function testVideoStreamingNotAllowedRedirect()
    {
        Config::set('flags.allow_video_streams', false);

        $video = Video::factory()->createOne();

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
        Config::set('flags.allow_video_streams', true);

        $video = Video::factory()->createOne();

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
        Config::set('flags.allow_video_streams', true);

        $video = Video::factory()->createOne();

        $response = $this->get(route('video.show', ['video' => $video]));

        static::assertInstanceOf(StreamedResponse::class, $response->baseResponse);
    }

    /**
     * If view recording is disabled, the video show route shall not record a view for the video.
     *
     * @return void
     */
    public function testViewRecordingNotAllowed()
    {
        Config::set('flags.allow_video_streams', true);
        Config::set('flags.allow_view_recording', false);

        $video = Video::factory()->createOne();

        $this->get(route('video.show', ['video' => $video]));

        static::assertEquals(0, $video->views()->count());
    }

    /**
     * If view recording is enabled, the video show route shall record a view for the video.
     *
     * @return void
     */
    public function testViewRecordingIsAllowed()
    {
        Config::set('flags.allow_video_streams', true);
        Config::set('flags.allow_view_recording', true);

        $video = Video::factory()->createOne();

        $this->get(route('video.show', ['video' => $video]));

        static::assertEquals(1, $video->views()->count());
    }

    /**
     * If view recording is enabled, the video show route shall record a view for the video.
     *
     * @return void
     */
    public function testViewRecordingCooldown()
    {
        Config::set('flags.allow_video_streams', true);
        Config::set('flags.allow_view_recording', true);

        $video = Video::factory()->createOne();

        Collection::times($this->faker->randomDigitNotNull(), function () use ($video) {
            $this->get(route('video.show', ['video' => $video]));
        });

        static::assertEquals(1, $video->views()->count());
    }
}
