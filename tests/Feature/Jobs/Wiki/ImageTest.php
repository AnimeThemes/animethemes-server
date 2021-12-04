<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Wiki;

use App\Constants\Config\FlagConstants;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Image;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
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
    public function testImageCreatedSendsDiscordNotification()
    {
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        Image::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an image is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testImageDeletedSendsDiscordNotification()
    {
        $image = Image::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $image->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an image is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testImageRestoredSendsDiscordNotification()
    {
        $image = Image::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $image->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an image is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testImageUpdatedSendsDiscordNotification()
    {
        $image = Image::factory()->createOne();

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = Image::factory()->makeOne();

        $image->fill($changes->getAttributes());
        $image->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
