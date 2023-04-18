<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Document;

use App\Constants\Config\FlagConstants;
use App\Events\Document\Page\PageCreated;
use App\Events\Document\Page\PageDeleted;
use App\Events\Document\Page\PageRestored;
use App\Events\Document\Page\PageUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Document\Page;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class PageTest.
 */
class PageTest extends TestCase
{
    /**
     * When n page is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testPageCreatedSendsDiscordNotification(): void
    {
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(PageCreated::class);

        Page::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a page is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testPageDeletedSendsDiscordNotification(): void
    {
        $page = Page::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(PageDeleted::class);

        $page->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a page is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testPageRestoredSendsDiscordNotification(): void
    {
        $page = Page::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(PageRestored::class);

        $page->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a page is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testPageUpdatedSendsDiscordNotification(): void
    {
        $page = Page::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(PageUpdated::class);

        $changes = Page::factory()->makeOne();

        $page->fill($changes->getAttributes());
        $page->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
