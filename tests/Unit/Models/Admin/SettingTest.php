<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Admin;

use App\Models\Admin\Setting;
use Tests\TestCase;

/**
 * Class SettingTest.
 */
class SettingTest extends TestCase
{
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
