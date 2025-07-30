<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Wiki\Video\Script\VideoScriptCreated;
use App\Events\Wiki\Video\Script\VideoScriptDeleted;
use App\Events\Wiki\Video\Script\VideoScriptRestored;
use App\Events\Wiki\Video\Script\VideoScriptUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('video created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(VideoScriptCreated::class);

    VideoScript::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('video deleted sends discord notification', function () {
    $script = VideoScript::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(VideoScriptDeleted::class);

    $script->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('video restored sends discord notification', function () {
    $script = VideoScript::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(VideoScriptRestored::class);

    $script->restore();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('video updated sends discord notification', function () {
    $script = VideoScript::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(VideoScriptUpdated::class);

    $changes = VideoScript::factory()->makeOne();

    $script->fill($changes->getAttributes());
    $script->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
