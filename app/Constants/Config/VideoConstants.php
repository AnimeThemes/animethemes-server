<?php

declare(strict_types=1);

namespace App\Constants\Config;

class VideoConstants
{
    final public const string DEFAULT_DISK_QUALIFIED = 'video.default_disk';

    final public const string DISKS_QUALIFIED = 'video.disks';

    final public const string NGINX_REDIRECT_QUALIFIED = 'video.nginx_redirect';

    final public const string PATH_QUALIFIED = 'video.path';

    final public const string RATE_LIMITER_QUALIFIED = 'video.rate_limiter';

    final public const string STREAMING_METHOD_QUALIFIED = 'video.streaming_method';

    final public const string URL_QUALIFIED = 'video.url';

    final public const string SCRIPT_DISK_QUALIFIED = 'video.script.disk';

    final public const string SCRIPT_PATH_QUALIFIED = 'video.script.path';

    final public const string SCRIPT_URL_QUALIFIED = 'video.script.url';
}
