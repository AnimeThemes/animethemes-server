<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        'images' => [
            'driver' => 's3',
            'key' => env('IMAGE_ACCESS_KEY_ID'),
            'secret' => env('IMAGE_SECRET_ACCESS_KEY'),
            'region' => env('IMAGE_DEFAULT_REGION'),
            'bucket' => env('IMAGE_BUCKET'),
            'endpoint' => env('IMAGE_ENDPOINT'),
            'stream_reads' => env('IMAGE_STREAM_READS'),
            'disable_asserts' => env('IMAGE_DISABLE_ASSERTS'),
            'visibility' => env('IMAGE_VISIBILITY'),
            'url' => env('IMAGE_URL'),
            'throw' => false,
            'retain_visibility' => false,
        ],

        'images_local' => [
            'driver' => 'local',
            'root' => public_path('images'),
            'url' => env('IMAGE_URL', env('APP_URL').'/images'),
            'visibility' => 'public',
            'throw' => false,
        ],

        'videos_nyc' => [
            'driver' => 's3',
            'key' => env('VIDEO_NYC_ACCESS_KEY_ID'),
            'secret' => env('VIDEO_NYC_SECRET_ACCESS_KEY'),
            'region' => env('VIDEO_NYC_DEFAULT_REGION'),
            'bucket' => env('VIDEO_NYC_BUCKET'),
            'endpoint' => env('VIDEO_NYC_ENDPOINT'),
            'stream_reads' => env('VIDEO_NYC_STREAM_READS'),
            'disable_asserts' => env('VIDEO_NYC_DISABLE_ASSERTS'),
            'visibility' => env('VIDEO_NYC_VISIBILITY'),
            'throw' => false,
            'retain_visibility' => false,
        ],

        'videos_fra' => [
            'driver' => 's3',
            'key' => env('VIDEO_FRA_ACCESS_KEY_ID'),
            'secret' => env('VIDEO_FRA_SECRET_ACCESS_KEY'),
            'region' => env('VIDEO_FRA_DEFAULT_REGION'),
            'bucket' => env('VIDEO_FRA_BUCKET'),
            'endpoint' => env('VIDEO_FRA_ENDPOINT'),
            'stream_reads' => env('VIDEO_FRA_STREAM_READS'),
            'disable_asserts' => env('VIDEO_FRA_DISABLE_ASSERTS'),
            'visibility' => env('VIDEO_FRA_VISIBILITY'),
            'throw' => false,
            'retain_visibility' => false,
        ],

        'videos_local' => [
            'driver' => 'local',
            'root' => public_path('videos'),
            'url' => env('APP_URL').'/videos',
            'visibility' => 'public',
            'throw' => false,
        ],

        'audios_nyc' => [
            'driver' => 's3',
            'key' => env('AUDIO_NYC_ACCESS_KEY_ID'),
            'secret' => env('AUDIO_NYC_SECRET_ACCESS_KEY'),
            'region' => env('AUDIO_NYC_DEFAULT_REGION'),
            'bucket' => env('AUDIO_NYC_BUCKET'),
            'endpoint' => env('AUDIO_NYC_ENDPOINT'),
            'stream_reads' => env('AUDIO_NYC_STREAM_READS'),
            'disable_asserts' => env('AUDIO_NYC_DISABLE_ASSERTS'),
            'visibility' => env('AUDIO_NYC_VISIBILITY'),
            'throw' => false,
            'retain_visibility' => false,
        ],

        'audios_fra' => [
            'driver' => 's3',
            'key' => env('AUDIO_FRA_ACCESS_KEY_ID'),
            'secret' => env('AUDIO_FRA_SECRET_ACCESS_KEY'),
            'region' => env('AUDIO_FRA_DEFAULT_REGION'),
            'bucket' => env('AUDIO_FRA_BUCKET'),
            'endpoint' => env('AUDIO_FRA_ENDPOINT'),
            'stream_reads' => env('AUDIO_FRA_STREAM_READS'),
            'disable_asserts' => env('AUDIO_FRA_DISABLE_ASSERTS'),
            'visibility' => env('AUDIO_FRA_VISIBILITY'),
            'throw' => false,
            'retain_visibility' => false,
        ],

        'audios_local' => [
            'driver' => 'local',
            'root' => public_path('audios'),
            'url' => env('APP_URL').'/audios',
            'visibility' => 'public',
            'throw' => false,
        ],

        'dumps' => [
            'driver' => 's3',
            'key' => env('DUMP_ACCESS_KEY_ID'),
            'secret' => env('DUMP_SECRET_ACCESS_KEY'),
            'region' => env('DUMP_DEFAULT_REGION'),
            'bucket' => env('DUMP_BUCKET'),
            'endpoint' => env('DUMP_ENDPOINT'),
            'stream_reads' => env('DUMP_STREAM_READS'),
            'disable_asserts' => env('DUMP_DISABLE_ASSERTS'),
            'visibility' => env('DUMP_VISIBILITY'),
            'throw' => false,
            'retain_visibility' => false,
        ],

        'dumps_local' => [
            'driver' => 'local',
            'root' => public_path('dumps'),
            'url' => env('APP_URL').'/dumps',
            'visibility' => 'public',
            'throw' => false,
        ],

        'scripts' => [
            'driver' => 's3',
            'key' => env('SCRIPT_ACCESS_KEY_ID'),
            'secret' => env('SCRIPT_SECRET_ACCESS_KEY'),
            'region' => env('SCRIPT_DEFAULT_REGION'),
            'bucket' => env('SCRIPT_BUCKET'),
            'endpoint' => env('SCRIPT_ENDPOINT'),
            'stream_reads' => env('SCRIPT_STREAM_READS'),
            'disable_asserts' => env('SCRIPT_DISABLE_ASSERTS'),
            'visibility' => env('SCRIPT_VISIBILITY'),
            'throw' => false,
            'retain_visibility' => false,
        ],

        'scripts_local' => [
            'driver' => 'local',
            'root' => public_path('scripts'),
            'url' => env('APP_URL').'/scripts',
            'visibility' => 'public',
            'throw' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
        public_path('audios') => env('AUDIO_DISK_ROOT', storage_path('app/audios')),
        public_path('images') => env('IMAGE_DISK_ROOT', storage_path('app/images')),
        public_path('videos') => env('VIDEO_DISK_ROOT', storage_path('app/videos')),
        public_path('dumps') => env('DUMP_DISK_ROOT', storage_path('app/dumps')),
        public_path('scripts') => env('SCRIPT_DISK_ROOT', storage_path('app/scripts')),
    ],

];
