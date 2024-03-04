<?php

declare(strict_types=1);

namespace Database\Seeders\Admin\Feature;

use App\Constants\FeatureConstants;
use App\Features\AllowAudioStreams;
use App\Features\AllowDumpDownloading;
use App\Features\AllowPlaylistManagement;
use App\Features\AllowScriptDownloading;
use App\Features\AllowVideoStreams;
use Illuminate\Database\Seeder;
use Laravel\Pennant\Feature;

/**
 * Class FeatureSeeder.
 */
class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Feature::deactivate(AllowAudioStreams::class);
        Feature::deactivate(AllowDumpDownloading::class);
        Feature::deactivate(AllowPlaylistManagement::class);
        Feature::deactivate(AllowScriptDownloading::class);
        Feature::deactivate(AllowVideoStreams::class);

        Feature::deactivate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Feature::deactivate(FeatureConstants::ALLOW_VIEW_RECORDING);
        Feature::activate(FeatureConstants::AUDIO_BITRATE_RESTRICTION);
        Feature::deactivate(FeatureConstants::REQUIRED_ENCODER_VERSION);
        Feature::activate(FeatureConstants::VIDEO_BITRATE_RESTRICTION);
        Feature::deactivate(FeatureConstants::VIDEO_CODEC_STREAM);
        Feature::deactivate(FeatureConstants::VIDEO_COLOR_PRIMARIES_STREAM);
        Feature::deactivate(FeatureConstants::VIDEO_COLOR_SPACE_STREAM);
        Feature::deactivate(FeatureConstants::VIDEO_COLOR_TRANSFER_STREAM);
        Feature::deactivate(FeatureConstants::VIDEO_PIXEL_FORMAT_STREAM);
    }
}
