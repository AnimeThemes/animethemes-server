<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Pivot\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Pivot\Wiki\StudioImage\StudioImageCreated;
use App\Events\Pivot\Wiki\StudioImage\StudioImageDeleted;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Image;
use App\Models\Wiki\Studio;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
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
    public function test_studio_image_created_sends_discord_notification(): void
    {
        $studio = Studio::factory()->createOne();
        $image = Image::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
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
    public function test_studio_image_deleted_sends_discord_notification(): void
    {
        $studio = Studio::factory()->createOne();
        $image = Image::factory()->createOne();

        $studio->images()->attach($image);

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(StudioImageDeleted::class);

        $studio->images()->detach($image);

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
