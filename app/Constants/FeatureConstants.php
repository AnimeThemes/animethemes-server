<?php

declare(strict_types=1);

namespace App\Constants;

class FeatureConstants
{
    final public const string ALLOW_DISCORD_NOTIFICATIONS = 'allow_discord_notifications';

    final public const string AUDIO_BITRATE_RESTRICTION = 'audio_bitrate_restriction';

    final public const string ENABLED_ONLY_ON_LOCALHOST = 'enabled_only_on_localhost';

    final public const string IGNORE_ALL_FILE_VALIDATIONS = 'ignore_all_file_validations';

    final public const string NULL_SCOPE = '__laravel_null';

    final public const string REQUIRED_ENCODER_VERSION = 'required_encoder_version';

    final public const string VIDEO_BITRATE_RESTRICTION = 'video_bitrate_restriction';

    final public const string VIDEO_CODEC_STREAM = 'video_codec_stream';

    final public const string VIDEO_COLOR_PRIMARIES_STREAM = 'video_color_primaries_stream';

    final public const string VIDEO_COLOR_SPACE_STREAM = 'video_color_space_stream';

    final public const string VIDEO_COLOR_TRANSFER_STREAM = 'video_color_transfer_stream';

    final public const string VIDEO_PIXEL_FORMAT_STREAM = 'video_pixel_format_stream';
}
