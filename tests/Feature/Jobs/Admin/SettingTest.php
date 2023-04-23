<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Admin;

use App\Constants\FeatureConstants;
use App\Events\Admin\Setting\SettingCreated;
use App\Events\Admin\Setting\SettingDeleted;
use App\Events\Admin\Setting\SettingUpdated;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Admin\Setting;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * Class SettingTest.
 */
class SettingTest extends TestCase
{
    /**
     * When a setting is created, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSettingCreatedSendsDiscordNotification(): void
    {
        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SettingCreated::class);

        Setting::factory()->createOne();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a setting is deleted, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSettingDeletedSendsDiscordNotification(): void
    {
        $setting = Setting::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SettingDeleted::class);

        $setting->delete();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }

    /**
     * When a setting is updated, a SendDiscordNotification job shall be dispatched.
     *
     * @return void
     */
    public function testSettingUpdatedSendsDiscordNotification(): void
    {
        $setting = Setting::factory()->createOne();

        Feature::activate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Bus::fake(SendDiscordNotificationJob::class);
        Event::fakeExcept(SettingUpdated::class);

        $changes = Setting::factory()->makeOne();

        $setting->fill($changes->getAttributes());
        $setting->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
