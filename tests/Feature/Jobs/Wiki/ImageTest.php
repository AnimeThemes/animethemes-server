<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Constants\FeatureConstants;
use App\Events\Wiki\Image\ImageCreated;
use App\Events\Wiki\Image\ImageDeleted;
use App\Events\Wiki\Image\ImageRestored;
use App\Events\Wiki\Image\ImageUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class ImageTest.
 */
class ImageTest extends TestCase
{
    /**
     * When an image is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_image_created_sends_discord_notification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ImageCreated::class);

        Image::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an image is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_image_deleted_sends_discord_notification(): void
    {
        $image = Image::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ImageDeleted::class);

        $image->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an image is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_image_restored_sends_discord_notification(): void
    {
        $image = Image::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ImageRestored::class);

        $image->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an image is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function test_image_updated_sends_discord_notification(): void
    {
        $image = Image::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ImageUpdated::class);

        $changes = Image::factory()->makeOne();

        $image->fill($changes->getAttributes());
        $image->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
