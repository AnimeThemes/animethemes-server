<?php

declare(strict_types=1);

namespace Jobs\Wiki;

use App\Jobs\SendDiscordNotificationJob;
use App\Models\Wiki\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class ImageTest.
 */
class ImageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * When an image is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testImageCreatedSendsDiscordNotification()
    {
        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        Image::factory()->create();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When an image is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testImageDeletedSendsDiscordNotification()
    {
        $image = Image::factory()->create();

        Config::set('app.allow_discord_notifications', true);
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
        $image = Image::factory()->create();

        Config::set('app.allow_discord_notifications', true);
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
        $image = Image::factory()->create();

        Config::set('app.allow_discord_notifications', true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = Image::factory()->make();

        $image->fill($changes->getAttributes());
        $image->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
