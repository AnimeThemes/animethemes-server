<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Wiki\Audio;

use App\Constants\Config\AudioConstants;
use App\Constants\Config\FlagConstants;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Http\StreamingMethod;
use App\Models\Auth\User;
use App\Models\Wiki\Audio;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

/**
 * Class AudioTest.
 */
class AudioTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * If audio streaming is disabled through the 'flags.allow_audio_streams' property,
     * the user shall receive a forbidden exception.
     *
     * @return void
     */
    public function testAudioStreamingNotAllowedForbidden(): void
    {
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED, false);

        $audio = Audio::factory()->createOne();

        $response = $this->get(route('audio.show', ['audio' => $audio]));

        $response->assertForbidden();
    }

    /**
     * Users with the bypass feature flag permission shall be permitted to stream audio
     * even if the 'flags.allow_audio_streams' property is disabled.
     *
     * @return void
     */
    public function testAudioStreamingPermittedForBypass(): void
    {
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED, $this->faker->boolean());
        Config::set(AudioConstants::STREAMING_METHOD_QUALIFIED, StreamingMethod::getRandomValue());

        $audio = Audio::factory()->createOne();

        $user = User::factory()->withPermissions(SpecialPermission::BYPASS_FEATURE_FLAGS)->createOne();

        Sanctum::actingAs($user);

        $response = $this->get(route('audio.show', ['audio' => $audio]));

        $response->assertSuccessful();
    }

    /**
     * If the audio is soft-deleted, the user shall receive a not found exception.
     *
     * @return void
     */
    public function testCannotStreamSoftDeletedAudio(): void
    {
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED, true);

        $audio = Audio::factory()->createOne();

        $audio->delete();

        $response = $this->get(route('audio.show', ['audio' => $audio]));

        $response->assertNotFound();
    }

    /**
     * If view recording is disabled, the audio show route shall not record a view for the audio.
     *
     * @return void
     */
    public function testViewRecordingNotAllowed(): void
    {
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED, true);
        Config::set(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED, false);

        $audio = Audio::factory()->createOne();

        $this->get(route('audio.show', ['audio' => $audio]));

        static::assertEquals(0, $audio->views()->count());
    }

    /**
     * If view recording is enabled, the audio show route shall record a view for the audio.
     *
     * @return void
     */
    public function testViewRecordingIsAllowed(): void
    {
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED, true);
        Config::set(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED, true);

        $audio = Audio::factory()->createOne();

        $this->get(route('audio.show', ['audio' => $audio]));

        static::assertEquals(1, $audio->views()->count());
    }

    /**
     * If view recording is enabled, the audio show route shall record a view for the audio.
     *
     * @return void
     */
    public function testViewRecordingCooldown(): void
    {
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED, true);
        Config::set(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED, true);

        $audio = Audio::factory()->createOne();

        Collection::times($this->faker->randomDigitNotNull(), function () use ($audio) {
            $this->get(route('audio.show', ['audio' => $audio]));
        });

        static::assertEquals(1, $audio->views()->count());
    }

    /**
     * If the streaming method is set to an unexpected value, the user shall receive an error.
     *
     * @return void
     */
    public function testInvalidStreamingMethodError(): void
    {
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED, true);
        Config::set(AudioConstants::STREAMING_METHOD_QUALIFIED, $this->faker->word());

        $audio = Audio::factory()->createOne();

        $response = $this->get(route('audio.show', ['audio' => $audio]));

        $response->assertServerError();
    }

    /**
     * If the streaming method is set to 'response', the audio shall be streamed through a Symfony StreamedResponse.
     *
     * @return void
     */
    public function testStreamedThroughResponse(): void
    {
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED, true);
        Config::set(AudioConstants::STREAMING_METHOD_QUALIFIED, StreamingMethod::RESPONSE);

        $audio = Audio::factory()->createOne();

        $response = $this->get(route('audio.show', ['audio' => $audio]));

        static::assertInstanceOf(StreamedResponse::class, $response->baseResponse);
    }

    /**
     * If the streaming method is set to 'nginx', the audio shall be streamed through a nginx internal redirect.
     *
     * @return void
     */
    public function testStreamedThroughNginxRedirect(): void
    {
        Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

        Config::set(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED, true);
        Config::set(AudioConstants::STREAMING_METHOD_QUALIFIED, StreamingMethod::NGINX);

        $audio = Audio::factory()->createOne();

        $response = $this->get(route('audio.show', ['audio' => $audio]));

        $response->assertHeader('X-Accel-Redirect');
    }
}
