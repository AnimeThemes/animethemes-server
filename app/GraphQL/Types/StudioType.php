<?php

declare(strict_types=1);

namespace App\GraphQL\Types;

use App\Models\Wiki\Studio;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class StudioType
{
    public function resolveSiteUrlAttribute(Studio $studio): string
    {
        return Str::of(Config::get('app.url'))
            ->append('/studio/')
            ->append($studio->slug)
            ->__toString();
    }
}
