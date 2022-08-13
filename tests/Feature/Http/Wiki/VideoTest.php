<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Wiki;

use App\Constants\Config\FlagConstants;
use App\Models\Wiki\Video;
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
    use WithFaker;
    use WithoutEvents;

    /**
     * If video streaming is disabled through the 'flags.allow_video_streams' property,
     * the user shall receive a forbidden exception.
     *
     * @return void
     */
    public function testVideoStreamingNotAllowedForbidden(): void
    {
        Config::set(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, false);

        $video = Video::factory()->createOne();

        $response = $this->get(route('video.show', ['video' => $video]));

        $response->assertForbidden();
    }

    /**
     * If the video is soft-deleted, the user shall receive a forbidden exception.
     *
     * @return void
     */
    public function testSoftDeleteVideoStreamingForbidden(): void
    {
        Config::set(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, true);

        $video = Video::factory()->createOne();

        $video->delete();

        $response = $this->get(route('video.show', ['video' => $video]));

        $response->assertForbidden();
    }

    /**
     * If view recording is disabled, the video show route shall not record a view for the video.
     *
     * @return void
     */
    public function testViewRecordingNotAllowed(): void
    {
        Config::set(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, true);
        Config::set(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED, false);

        $video = Video::factory()->createOne();

        $this->get(route('video.show', ['video' => $video]));

        static::assertEquals(0, $video->views()->count());
    }

    /**
     * If view recording is enabled, the video show route shall record a view for the video.
     *
     * @return void
     */
    public function testViewRecordingIsAllowed(): void
    {
        Config::set(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, true);
        Config::set(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED, true);

        $video = Video::factory()->createOne();

        $this->get(route('video.show', ['video' => $video]));

        static::assertEquals(1, $video->views()->count());
    }

    /**
     * If view recording is enabled, the video show route shall record a view for the video.
     *
     * @return void
     */
    public function testViewRecordingCooldown(): void
    {
        Config::set(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, true);
        Config::set(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED, true);

        $video = Video::factory()->createOne();

        Collection::times($this->faker->randomDigitNotNull(), function () use ($video) {
            $this->get(route('video.show', ['video' => $video]));
        });

        static::assertEquals(1, $video->views()->count());
    }

    /**
     * If the streaming method is set to 'response', the video shall be streamed through a Symfony StreamedResponse.
     *
     * @return void
     */
    public function testStreamedThroughResponse(): void
    {
        Config::set(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, true);
        Config::set('video.streaming_method', 'response');

        $video = Video::factory()->createOne();

        $response = $this->get(route('video.show', ['video' => $video]));

        static::assertInstanceOf(StreamedResponse::class, $response->baseResponse);
    }
}
