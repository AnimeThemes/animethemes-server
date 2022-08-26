<?php

declare(strict_types=1);

use App\Constants\Config\FlagConstants;
use App\Constants\Config\VideoConstants;
use App\Constants\Config\WikiConstants;
use App\Models\Admin\Setting;

return [

    /*
    |--------------------------------------------------------------------------
    | Enable / Disable auto save
    |--------------------------------------------------------------------------
    |
    | Auto-save every time the application shuts down
    |
    */
    'auto_save'         => false,

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Options for caching. Set whether to enable cache, its key, time to live
    | in seconds and whether to auto clear after save.
    |
    */
    'cache' => [
        'enabled'       => false,
        'key'           => 'setting',
        'ttl'           => 3600,
        'auto_clear'    => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Setting driver
    |--------------------------------------------------------------------------
    |
    | Select where to store the settings.
    |
    | Supported: "database", "json", "memory"
    |
    */
    'driver'            => 'database',

    /*
    |--------------------------------------------------------------------------
    | Database driver
    |--------------------------------------------------------------------------
    |
    | Options for database driver. Enter which connection to use, null means
    | the default connection. Set the table and column names.
    |
    */
    'database' => [
        'connection'    => null,
        'table'         => Setting::TABLE,
        'key'           => Setting::ATTRIBUTE_KEY,
        'value'         => Setting::ATTRIBUTE_VALUE,
    ],

    /*
    |--------------------------------------------------------------------------
    | JSON driver
    |--------------------------------------------------------------------------
    |
    | Options for json driver. Enter the full path to the .json file.
    |
    */
    'json' => [
        'path'          => storage_path() . '/settings.json',
    ],

    /*
    |--------------------------------------------------------------------------
    | Override application config values
    |--------------------------------------------------------------------------
    |
    | If defined, settings package will override these config values.
    |
    | Sample:
    |   "app.locale" => "settings.locale",
    |
    */
    'override' => [
        FlagConstants::ALLOW_AUDIO_STREAMS_FLAG_QUALIFIED => FlagConstants::ALLOW_AUDIO_STREAMS_FLAG,
        FlagConstants::ALLOW_VIDEO_STREAMS_FLAG_QUALIFIED => FlagConstants::ALLOW_VIDEO_STREAMS_FLAG,
        FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG_QUALIFIED => FlagConstants::ALLOW_DISCORD_NOTIFICATIONS_FLAG,
        FlagConstants::ALLOW_VIEW_RECORDING_FLAG_QUALIFIED => FlagConstants::ALLOW_VIEW_RECORDING_FLAG,
        VideoConstants::ENCODER_VERSION_QUALIFIED => VideoConstants::ENCODER_VERSION,
        WikiConstants::FEATURED_ENTRY_SETTING_QUALIFIED => WikiConstants::FEATURED_ENTRY_SETTING,
        WikiConstants::FEATURED_VIDEO_SETTING_QUALIFIED => WikiConstants::FEATURED_VIDEO_SETTING,
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback
    |--------------------------------------------------------------------------
    |
    | Define fallback settings to be used in case the default is null
    |
    | Sample:
    |   "currency" => "USD",
    |
    */
    'fallback' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Required Extra Columns
    |--------------------------------------------------------------------------
    |
    | The list of columns required to be set up
    |
    | Sample:
    |   "user_id",
    |   "tenant_id",
    |
    */
    'required_extra_columns' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Encryption
    |--------------------------------------------------------------------------
    |
    | Define the keys which should be crypt automatically.
    |
    | Sample:
    |   "payment.key"
    |
    */
   'encrypted_keys' => [

   ],

];
