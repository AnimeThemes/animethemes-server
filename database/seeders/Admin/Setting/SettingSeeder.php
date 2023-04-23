<?php

declare(strict_types=1);

namespace Database\Seeders\Admin\Setting;

use App\Constants\Config\WikiConstants;
use App\Models\Admin\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

/**
 * Class SettingSeeder.
 */
class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Setting::query()->firstOrCreate(
            [
                Setting::ATTRIBUTE_KEY => WikiConstants::FEATURED_ENTRY_SETTING,
            ],
            [
                Setting::ATTRIBUTE_KEY => WikiConstants::FEATURED_ENTRY_SETTING,
                Setting::ATTRIBUTE_VALUE => Config::get(WikiConstants::FEATURED_ENTRY_SETTING_QUALIFIED, ''),
            ]
        );

        Setting::query()->firstOrCreate(
            [
                Setting::ATTRIBUTE_KEY => WikiConstants::FEATURED_VIDEO_SETTING,
            ],
            [
                Setting::ATTRIBUTE_KEY => WikiConstants::FEATURED_VIDEO_SETTING,
                Setting::ATTRIBUTE_VALUE => Config::get(WikiConstants::FEATURED_VIDEO_SETTING_QUALIFIED, ''),
            ]
        );
    }
}
