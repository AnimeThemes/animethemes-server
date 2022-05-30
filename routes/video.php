<?php

declare(strict_types=1);

use Spatie\RouteDiscovery\Discovery\Discover;

Discover::controllers()->in(app_path('Http/Controllers/Wiki'));
