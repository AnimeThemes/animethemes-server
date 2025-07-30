<?php

declare(strict_types=1);

use App\Constants\Config\AudioConstants;
use App\Constants\FeatureConstants;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Http\StreamingMethod;
use App\Features\AllowAudioStreams;
use App\Models\Auth\User;
use App\Models\Wiki\Audio;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\StreamedResponse;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('audio streaming not allowed forbidden', function () {
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    Feature::deactivate(AllowAudioStreams::class);

    $audio = Audio::factory()->createOne();

    $response = $this->get(route('audio.show', ['audio' => $audio]));

    $response->assertForbidden();
});

test('audio streaming permitted for bypass', function () {
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowAudioStreams::class, fake()->boolean());

    /** @var StreamingMethod $streamingMethod */
    $streamingMethod = Arr::random(StreamingMethod::cases());
    Config::set(AudioConstants::STREAMING_METHOD_QUALIFIED, $streamingMethod->value);

    $audio = Audio::factory()->createOne();

    $user = User::factory()->withPermissions(SpecialPermission::BYPASS_FEATURE_FLAGS->value)->createOne();

    Sanctum::actingAs($user);

    $response = $this->get(route('audio.show', ['audio' => $audio]));

    $response->assertSuccessful();
});

test('cannot stream soft deleted audio', function () {
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowAudioStreams::class);

    $audio = Audio::factory()->trashed()->createOne();

    $response = $this->get(route('audio.show', ['audio' => $audio]));

    $response->assertNotFound();
});

test('view recording not allowed', function () {
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowAudioStreams::class);
    Feature::deactivate(FeatureConstants::ALLOW_VIEW_RECORDING);

    $audio = Audio::factory()->createOne();

    $this->get(route('audio.show', ['audio' => $audio]));

    static::assertEquals(0, $audio->views()->count());
});

test('view recording is allowed', function () {
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowAudioStreams::class);
    Feature::activate(FeatureConstants::ALLOW_VIEW_RECORDING);

    $audio = Audio::factory()->createOne();

    $this->get(route('audio.show', ['audio' => $audio]));

    static::assertEquals(1, $audio->views()->count());
});

test('view recording cooldown', function () {
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowAudioStreams::class);
    Feature::activate(FeatureConstants::ALLOW_VIEW_RECORDING);

    $audio = Audio::factory()->createOne();

    Collection::times(fake()->randomDigitNotNull(), function () use ($audio) {
        $this->get(route('audio.show', ['audio' => $audio]));
    });

    static::assertEquals(1, $audio->views()->count());
});

test('invalid streaming method error', function () {
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowAudioStreams::class);
    Config::set(AudioConstants::STREAMING_METHOD_QUALIFIED, fake()->word());

    $audio = Audio::factory()->createOne();

    $response = $this->get(route('audio.show', ['audio' => $audio]));

    $response->assertServerError();
});

test('streamed through response', function () {
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowAudioStreams::class);
    Config::set(AudioConstants::STREAMING_METHOD_QUALIFIED, StreamingMethod::RESPONSE->value);

    $audio = Audio::factory()->createOne();

    $response = $this->get(route('audio.show', ['audio' => $audio]));

    static::assertInstanceOf(StreamedResponse::class, $response->baseResponse);
});

test('streamed through nginx redirect', function () {
    Storage::fake(Config::get(AudioConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowAudioStreams::class);
    Config::set(AudioConstants::STREAMING_METHOD_QUALIFIED, StreamingMethod::NGINX->value);

    $audio = Audio::factory()->createOne();

    $response = $this->get(route('audio.show', ['audio' => $audio]));

    $response->assertHeader('X-Accel-Redirect');
});
