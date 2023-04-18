<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Constants\Config\FlagConstants;
use App\Events\Wiki\Image\ImageCreated;
use App\Events\Wiki\Image\ImageDeleted;
use App\Events\Wiki\Image\ImageRestored;
use App\Events\Wiki\Image\ImageUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
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
    public function testImageCreatedSendsDiscordNotification(): void
    {
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
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
    public function testImageDeletedSendsDiscordNotification(): void
    {
        $image = Image::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
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
    public function testImageRestoredSendsDiscordNotification(): void
    {
        $image = Image::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
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
    public function testImageUpdatedSendsDiscordNotification(): void
    {
        $image = Image::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(ImageUpdated::class);

        $changes = Image::factory()->makeOne();

        $image->fill($changes->getAttributes());
        $image->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
