<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Admin;

use App\Models\Admin\Setting;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class SettingTest.
 */
class SettingTest extends TestCase
{
    /**
     * Setting shall be auditable.
     *
     * @return void
     */
    public function testAuditable(): void
    {
        Config::set('audit.console', true);

        $setting = Setting::factory()->createOne();

        static::assertEquals(1, $setting->audits()->count());
    }

    /**
     * Settings shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $setting = Setting::factory()->createOne();

        static::assertIsString($setting->getName());
    }
}
