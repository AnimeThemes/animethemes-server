<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Wiki\Video;

use App\Constants\Config\FlagConstants;
use App\Constants\Config\VideoConstants;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Http\StreamingMethod;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
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
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, false);

        $video = Video::factory()->createOne();

        $response = $this->get(route('video.show', ['video' => $video]));

        $response->assertForbidden();
    }

    /**
     * If the video is soft-deleted, the user shall receive a not found exception.
     *
     * @return void
     */
    public function testCannotStreamSoftDeletedVideo(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, true);

        $video = Video::factory()->createOne();

        $video->delete();

        $response = $this->get(route('video.show', ['video' => $video]));

        $response->assertNotFound();
    }

    /**
     * If view recording is disabled, the video show route shall not record a view for the video.
     *
     * @return void
     */
    public function testViewRecordingNotAllowed(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, true);
        Config::set(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED, false);

        $video = Video::factory()->createOne();

        $this->get(route('video.show', ['video' => $video]));

        static::assertEquals(0, $video->views()->count());
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to stream video
     * even if the 'flags.allow_video_streams' property is disabled.
     *
     * @return void
     */
    public function testVideoStreamingPermittedForBypass(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, $this->faker->boolean());
        Config::set(VideoConstants::STREAMING_METHOD_QUALIFIED, StreamingMethod::getRandomValue());

        $video = Video::factory()->createOne();

        $user = User::factory()->withPermission(SpecialPermission::BYPASS_FEATURE_FLAGS)->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('video.show', ['video' => $video]));

        $response->assertSuccessful();
    }

    /**
     * If view recording is enabled, the video show route shall record a view for the video.
     *
     * @return void
     */
    public function testViewRecordingIsAllowed(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

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
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, true);
        Config::set(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED, true);

        $video = Video::factory()->createOne();

        Collection::times($this->faker->randomDigitNotNull(), function () use ($video) {
            $this->get(route('video.show', ['video' => $video]));
        });

        static::assertEquals(1, $video->views()->count());
    }

    /**
     * If the streaming method is set to an unexpected value, the user shall receive an error.
     *
     * @return void
     */
    public function testInvalidStreamingMethodError(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, true);
        Config::set(VideoConstants::STREAMING_METHOD_QUALIFIED, $this->faker->word());

        $video = Video::factory()->createOne();

        $response = $this->get(route('video.show', ['video' => $video]));

        $response->assertServerError();
    }

    /**
     * If the streaming method is set to 'response', the video shall be streamed through a Symfony StreamedResponse.
     *
     * @return void
     */
    public function testStreamedThroughResponse(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, true);
        Config::set(VideoConstants::STREAMING_METHOD_QUALIFIED, StreamingMethod::RESPONSE);

        $video = Video::factory()->createOne();

        $response = $this->get(route('video.show', ['video' => $video]));

        static::assertInstanceOf(StreamedResponse::class, $response->baseResponse);
    }

    /**
     * If the streaming method is set to 'nginx', the video shall be streamed through a nginx internal redirect.
     *
     * @return void
     */
    public function testStreamedThroughNginxRedirect(): void
    {
        Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED, true);
        Config::set(VideoConstants::STREAMING_METHOD_QUALIFIED, StreamingMethod::NGINX);

        $video = Video::factory()->createOne();

        $response = $this->get(route('video.show', ['video' => $video]));

        $response->assertHeader('X-Accel-Redirect');
    }
}
