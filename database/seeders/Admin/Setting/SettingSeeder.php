<?php

declare(strict_types=1);

namespace Database\Seeders\Admin\Setting;

use App\Constants\Config\FlagConstants;
use App\Constants\Config\VideoConstants;
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
                Setting::ATTRIBUTE_KEY => FlagConstants::ALLOW_AUDIO_STREAMS_FLAG,
            ],
            [
                Setting::ATTRIBUTE_KEY => FlagConstants::ALLOW_AUDIO_STREAMS_FLAG,
                Setting::ATTRIBUTE_VALUE => 'false',
            ]
        );

        Setting::query()->firstOrCreate(
            [
                Setting::ATTRIBUTE_KEY => FlagConstants::ALLOW_VIDEO_STREAMS_FLAG,
            ],
            [
                Setting::ATTRIBUTE_KEY => FlagConstants::ALLOW_VIDEO_STREAMS_FLAG,
                Setting::ATTRIBUTE_VALUE => 'false',
            ]
        );

        Setting::query()->firstOrCreate(
            [
                Setting::ATTRIBUTE_KEY => FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG,
            ],
            [
                Setting::ATTRIBUTE_KEY => FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG,
                Setting::ATTRIBUTE_VALUE => 'false',
            ]
        );

        Setting::query()->firstOrCreate(
            [
                Setting::ATTRIBUTE_KEY => FlagConstants::ALLOW_VIEW_RECORDING_FLAG,
            ],
            [
                Setting::ATTRIBUTE_KEY => FlagConstants::ALLOW_VIEW_RECORDING_FLAG,
                Setting::ATTRIBUTE_VALUE => 'false',
            ]
        );

        Setting::query()->firstOrCreate(
            [
                Setting::ATTRIBUTE_KEY => FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG,
            ],
            [
                Setting::ATTRIBUTE_KEY => FlagConstants::ALLOW_DUMP_DOWNLOADING_FLAG,
                Setting::ATTRIBUTE_VALUE => 'false',
            ]
        );

        Setting::query()->firstOrCreate(
            [
                Setting::ATTRIBUTE_KEY => FlagConstants::ALLOW_SCRIPT_DOWNLOADING_FLAG,
            ],
            [
                Setting::ATTRIBUTE_KEY => FlagConstants::ALLOW_SCRIPT_DOWNLOADING_FLAG,
                Setting::ATTRIBUTE_VALUE => 'false',
            ]
        );

        Setting::query()->firstOrCreate(
            [
                Setting::ATTRIBUTE_KEY => VideoConstants::ENCODER_VERSION,
            ],
            [
                Setting::ATTRIBUTE_KEY => VideoConstants::ENCODER_VERSION,
                Setting::ATTRIBUTE_VALUE => Config::get(VideoConstants::ENCODER_VERSION_QUALIFIED, ''),
            ]
        );

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
