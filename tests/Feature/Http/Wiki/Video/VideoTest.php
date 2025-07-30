<?php

declare(strict_types=1);

use App\Constants\Config\VideoConstants;
use App\Constants\FeatureConstants;
use App\Enums\Auth\SpecialPermission;
use App\Enums\Http\StreamingMethod;
use App\Events\Wiki\Video\VideoThrottled;
use App\Features\AllowVideoStreams;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Auth\User;
use App\Models\Wiki\Video;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\StreamedResponse;

uses(Illuminate\Foundation\Testing\WithFaker::class);

test('video streaming not allowed forbidden', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    Feature::deactivate(AllowVideoStreams::class);

    $video = Video::factory()->createOne();

    $response = $this->get(route('video.show', ['video' => $video]));

    $response->assertForbidden();
});

test('cannot stream soft deleted video', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowVideoStreams::class);

    $video = Video::factory()->trashed()->createOne();

    $response = $this->get(route('video.show', ['video' => $video]));

    $response->assertNotFound();
});

test('view recording not allowed', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowVideoStreams::class);
    Feature::deactivate(FeatureConstants::ALLOW_VIEW_RECORDING);

    $video = Video::factory()->createOne();

    $this->get(route('video.show', ['video' => $video]));

    static::assertEquals(0, $video->views()->count());
});

test('video streaming permitted for bypass', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowVideoStreams::class, fake()->boolean());

    /** @var StreamingMethod $streamingMethod */
    $streamingMethod = Arr::random(StreamingMethod::cases());
    Config::set(VideoConstants::STREAMING_METHOD_QUALIFIED, $streamingMethod->value);

    $video = Video::factory()->createOne();

    $user = User::factory()->withPermissions(SpecialPermission::BYPASS_FEATURE_FLAGS->value)->createOne();

    Sanctum::actingAs($user);

    $response = $this->get(route('video.show', ['video' => $video]));

    $response->assertSuccessful();
});

test('view recording is allowed', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowVideoStreams::class);
    Feature::activate(FeatureConstants::ALLOW_VIEW_RECORDING);

    $video = Video::factory()->createOne();

    $this->get(route('video.show', ['video' => $video]));

    static::assertEquals(1, $video->views()->count());
});

test('view recording cooldown', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowVideoStreams::class);
    Feature::activate(FeatureConstants::ALLOW_VIEW_RECORDING);

    $video = Video::factory()->createOne();

    Collection::times(fake()->randomDigitNotNull(), function () use ($video) {
        $this->get(route('video.show', ['video' => $video]));
    });

    static::assertEquals(1, $video->views()->count());
});

test('invalid streaming method error', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowVideoStreams::class);
    Config::set(VideoConstants::STREAMING_METHOD_QUALIFIED, fake()->word());

    $video = Video::factory()->createOne();

    $response = $this->get(route('video.show', ['video' => $video]));

    $response->assertServerError();
});

test('streamed through response', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowVideoStreams::class);
    Config::set(VideoConstants::STREAMING_METHOD_QUALIFIED, StreamingMethod::RESPONSE->value);

    $video = Video::factory()->createOne();

    $response = $this->get(route('video.show', ['video' => $video]));

    static::assertInstanceOf(StreamedResponse::class, $response->baseResponse);
});

test('streamed through nginx redirect', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowVideoStreams::class);
    Config::set(VideoConstants::STREAMING_METHOD_QUALIFIED, StreamingMethod::NGINX->value);

    $video = Video::factory()->createOne();

    $response = $this->get(route('video.show', ['video' => $video]));

    $response->assertHeader('X-Accel-Redirect');
});

test('not throttled', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowVideoStreams::class);
    Config::set(VideoConstants::STREAMING_METHOD_QUALIFIED, Arr::random(StreamingMethod::cases())->value);
    Config::set(VideoConstants::RATE_LIMITER_QUALIFIED, -1);

    $video = Video::factory()->createOne();

    $response = $this->get(route('video.show', ['video' => $video]));

    $response->assertHeaderMissing('X-RateLimit-Limit');
    $response->assertHeaderMissing('X-RateLimit-Remaining');
});

test('rate limited', function () {
    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowVideoStreams::class);
    Config::set(VideoConstants::STREAMING_METHOD_QUALIFIED, Arr::random(StreamingMethod::cases())->value);
    Config::set(VideoConstants::RATE_LIMITER_QUALIFIED, fake()->randomDigitNotNull());

    $video = Video::factory()->createOne();

    $response = $this->get(route('video.show', ['video' => $video]));

    $response->assertHeader('X-RateLimit-Limit');
    $response->assertHeader('X-RateLimit-Remaining');
});

test('throttled event', function () {
    $limit = fake()->randomDigitNotNull();

    Event::fake();

    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    Feature::activate(AllowVideoStreams::class);
    Config::set(VideoConstants::STREAMING_METHOD_QUALIFIED, Arr::random(StreamingMethod::cases())->value);
    Config::set(VideoConstants::RATE_LIMITER_QUALIFIED, $limit);

    $video = Video::factory()->createOne();

    Collection::times($limit + 1, function () use ($video) {
        $this->get(route('video.show', ['video' => $video]));
    });

    Event::assertDispatched(VideoThrottled::class);
});

test('throttled notification', function () {
    $limit = fake()->randomDigitNotNull();

    Storage::fake(Config::get(VideoConstants::DEFAULT_DISK_QUALIFIED));

    Bus::fake(SendDiscordNotificationJob::class);

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Feature::activate(AllowVideoStreams::class);
    Config::set(VideoConstants::STREAMING_METHOD_QUALIFIED, Arr::random(StreamingMethod::cases())->value);
    Config::set(VideoConstants::RATE_LIMITER_QUALIFIED, $limit);
    Event::fakeExcept(VideoThrottled::class);

    $video = Video::factory()->createOne();

    Collection::times($limit + 1, function () use ($video) {
        $this->get(route('video.show', ['video' => $video]));
    });

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
