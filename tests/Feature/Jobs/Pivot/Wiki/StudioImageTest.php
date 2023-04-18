<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

use App\Constants\Config\FlagConstants;
use App\Events\Pivot\Wiki\StudioImage\StudioImageCreated;
use App\Events\Pivot\Wiki\StudioImage\StudioImageDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class StudioImageTest.
 */
class StudioImageTest extends TestCase
{
    /**
     * When a Studio is attached to an Image or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testStudioImageCreatedSendsDiscordNotification(): void
    {
        $studio = Studio::factory()->createOne();
        $image = Image::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(StudioImageCreated::class);

        $studio->images()->attach($image);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a Studio is detached from an Image or vice versa, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testStudioImageDeletedSendsDiscordNotification(): void
    {
        $studio = Studio::factory()->createOne();
        $image = Image::factory()->createOne();

        $studio->images()->attach($image);

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(StudioImageDeleted::class);

        $studio->images()->detach($image);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
