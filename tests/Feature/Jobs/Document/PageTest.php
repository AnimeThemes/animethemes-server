<?php

declare(strict_types=1);

use App\Constants\FeatureConstants;
use App\Events\Document\Page\PageCreated;
use App\Events\Document\Page\PageDeleted;
use App\Events\Document\Page\PageRestored;
use App\Events\Document\Page\PageUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Document\Page;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;

test('page created sends discord notification', function () {
    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(PageCreated::class);

    Page::factory()->createOne();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('page deleted sends discord notification', function () {
    $page = Page::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(PageDeleted::class);

    $page->delete();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('page restored sends discord notification', function () {
    $page = Page::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(PageRestored::class);

    $page->restore();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});

test('page updated sends discord notification', function () {
    $page = Page::factory()->createOne();

    Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
    Bus::fake(SendDiscordNotificationJob::class);
    Event::fakeExcept(PageUpdated::class);

    $changes = Page::factory()->makeOne();

    $page->fill($changes->getAttributes());
    $page->save();

    Bus::assertDispatched(SendDiscordNotificationJob::class);
});
