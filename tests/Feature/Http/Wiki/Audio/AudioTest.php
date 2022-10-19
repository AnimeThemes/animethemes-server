<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Wiki\Audio;

use App\Constants\Config\FlagConstants;
use App\Models\Wiki\Audio;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
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
        Config::set(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED, false);

        $audio = Audio::factory()->createOne();

        $response = $this->get(route('audio.show', ['audio' => $audio]));

        $response->assertForbidden();
    }

    /**
     * If the audio is soft-deleted, the user shall receive a not found exception.
     *
     * @return void
     */
    public function testCannotStreamSoftDeletedAudio(): void
    {
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
        Config::set(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED, true);
        Config::set(FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED, true);

        $audio = Audio::factory()->createOne();

        Collection::times($this->faker->randomDigitNotNull(), function () use ($audio) {
            $this->get(route('audio.show', ['audio' => $audio]));
        });

        static::assertEquals(1, $audio->views()->count());
    }

    /**
     * If the streaming method is set to 'response', the audio shall be streamed through a Symfony StreamedResponse.
     *
     * @return void
     */
    public function testStreamedThroughResponse(): void
    {
        Config::set(FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED, true);
        Config::set('audio.streaming_method', 'response');

        $audio = Audio::factory()->createOne();

        $response = $this->get(route('audio.show', ['audio' => $audio]));

        static::assertInstanceOf(StreamedResponse::class, $response->baseResponse);
    }
}
