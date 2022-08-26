<?php

declare(strict_types=1);

namespace Tests\Feature\Events\Admin;

use App\Events\Admin\Setting\SettingCreated;
use App\Events\Admin\Setting\SettingDeleted;
use App\Events\Admin\Setting\SettingUpdated;
use App\Models\Admin\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class SettingTest.
 */
class SettingTest extends TestCase
{
    /**
     * When a Setting is created, a SettingCreated event shall be dispatched.
     *
     * @return void
     */
    public function testSettingCreatedEventDispatched(): void
    {
        Event::fake();

        Setting::factory()->create();

        Event::assertDispatched(SettingCreated::class);
    }

    /**
     * When a Setting is deleted, a SettingDeleted event shall be dispatched.
     *
     * @return void
     */
    public function testSettingDeletedEventDispatched(): void
    {
        Event::fake();

        $setting = Setting::factory()->create();

        $setting->delete();

        Event::assertDispatched(SettingDeleted::class);
    }

    /**
     * When a Setting is updated, a SettingUpdated event shall be dispatched.
     *
     * @return void
     */
    public function testSettingUpdatedEventDispatched(): void
    {
        Event::fake();

        $setting = Setting::factory()->createOne();
        $changes = Setting::factory()->makeOne();

        $setting->fill($changes->getAttributes());
        $setting->save();

        Event::assertDispatched(SettingUpdated::class);
    }

    /**
     * The SettingUpdated event shall contain embed fields.
     *
     * @return void
     */
    public function testSettingUpdatedEventEmbedFields(): void
    {
        Event::fake();

        $setting = Setting::factory()->createOne();
        $changes = Setting::factory()->makeOne();

        $setting->fill($changes->getAttributes());
        $setting->save();

        Event::assertDispatched(SettingUpdated::class, function (SettingUpdated $event) {
            $message = $event->getDiscordMessage();

            return ! empty(Arr::get($message->embed, 'fields'));
        });
    }
}
