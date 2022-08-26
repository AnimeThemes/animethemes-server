<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs\Admin;

use App\Constants\Config\FlagConstants;
use App\Jobs\SendDiscordNotificationJob;
use App\Models\Admin\Setting;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
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
        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

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

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

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

        Config::set(FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED, true);
        Bus::fake(SendDiscordNotificationJob::class);

        $changes = Setting::factory()->makeOne();

        $setting->fill($changes->getAttributes());
        $setting->save();

        Bus::assertDispatched(SendDiscordNotificationJob::class);
    }
}
