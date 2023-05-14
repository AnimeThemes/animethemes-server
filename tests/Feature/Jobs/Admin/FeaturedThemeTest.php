<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Admin;

use App\Constants\FeatureConstants;
use App\Events\Admin\FeaturedTheme\FeaturedThemeCreated;
use App\Events\Admin\FeaturedTheme\FeaturedThemeDeleted;
use App\Events\Admin\FeaturedTheme\FeaturedThemeRestored;
use App\Events\Admin\FeaturedTheme\FeaturedThemeUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Admin\FeaturedTheme;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class FeaturedThemeTest.
 */
class FeaturedThemeTest extends TestCase
{
    /**
     * When a featured theme is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testFeaturedThemeCreatedSendsDiscordNotification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(FeaturedThemeCreated::class);

        FeaturedTheme::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a featured theme is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testFeaturedThemeDeletedSendsDiscordNotification(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(FeaturedThemeDeleted::class);

        $featuredTheme->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a featured theme is restored, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testFeaturedThemeRestoredSendsDiscordNotification(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(FeaturedThemeRestored::class);

        $featuredTheme->restore();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a featured theme is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testFeaturedThemeUpdatedSendsDiscordNotification(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(FeaturedThemeUpdated::class);

        $changes = FeaturedTheme::factory()->makeOne();

        $featuredTheme->fill($changes->getAttributes());
        $featuredTheme->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
