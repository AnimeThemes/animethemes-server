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
        ],

        'images_local' => [
            'driver' => 'local',
            'root' => public_path('images'),
            'url' => env('APP_URL').'/images',
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
        ],

        'videos_local' => [
            'driver' => 'local',
            'root' => public_path('videos'),
            'url' => env('APP_URL').'/videos',
            'visibility' => 'public',
            'throw' => false,
        ],

        'audios' => [
            'driver' => 's3',
            'key' => env('AUDIO_ACCESS_KEY_ID'),
            'secret' => env('AUDIO_SECRET_ACCESS_KEY'),
            'region' => env('AUDIO_DEFAULT_REGION'),
            'bucket' => env('AUDIO_BUCKET'),
            'endpoint' => env('AUDIO_ENDPOINT'),
            'stream_reads' => env('AUDIO_STREAM_READS'),
            'disable_asserts' => env('AUDIO_DISABLE_ASSERTS'),
            'visibility' => env('AUDIO_VISIBILITY'),
            'throw' => false,
        ],

        'audios_local' => [
            'driver' => 'local',
            'root' => public_path('audios'),
            'url' => env('APP_URL').'/audios',
            'visibility' => 'public',
            'throw' => false,
        ],

        'db-dumps' => [
            'driver' => 'local',
            'root' => storage_path('db-dumps'),
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
    ],

];
