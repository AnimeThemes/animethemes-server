<?php

declare(strict_types=1);

namespace Database\Seeders\Admin\Feature;

use App\Constants\FeatureConstants;
use App\Features\AllowAudioStreams;
use App\Features\AllowDumpDownloading;
use App\Features\AllowExternalProfileManagement;
use App\Features\AllowPlaylistManagement;
use App\Features\AllowScriptDownloading;
use App\Features\AllowSubmission;
use App\Features\AllowVideoStreams;
use Illuminate\Database\Seeder;
use Laravel\Pennant\Feature;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Feature::deactivate(AllowAudioStreams::class);
        Feature::deactivate(AllowDumpDownloading::class);
        Feature::deactivate(AllowExternalProfileManagement::class);
        Feature::deactivate(AllowPlaylistManagement::class);
        Feature::deactivate(AllowSubmission::class);
        Feature::deactivate(AllowScriptDownloading::class);
        Feature::deactivate(AllowVideoStreams::class);

        Feature::deactivate(FeatureConstants::ALLOW_DISCORD_NOTIFICATIONS);
        Feature::activate(FeatureConstants::AUDIO_BITRATE_RESTRICTION);
        Feature::activate(FeatureConstants::ENABLED_ONLY_ON_LOCALHOST);
        Feature::deactivate(FeatureConstants::IGNORE_ALL_FILE_VALIDATIONS);
        Feature::activate(FeatureConstants::REQUIRED_ENCODER_VERSION, 'Lavf59.27.100');
        Feature::activate(FeatureConstants::VIDEO_BITRATE_RESTRICTION);
        Feature::activate(FeatureConstants::VIDEO_CODEC_STREAM, 'vp9');
        Feature::activate(FeatureConstants::VIDEO_COLOR_PRIMARIES_STREAM, 'bt709,smpte170m,bt470bg');
        Feature::activate(FeatureConstants::VIDEO_COLOR_SPACE_STREAM, 'bt709,smpte170m,bt470bg');
        Feature::activate(FeatureConstants::VIDEO_COLOR_TRANSFER_STREAM, 'bt709,smpte170m,bt470bg');
        Feature::activate(FeatureConstants::VIDEO_PIXEL_FORMAT_STREAM, 'yuv420p');
    }
}
