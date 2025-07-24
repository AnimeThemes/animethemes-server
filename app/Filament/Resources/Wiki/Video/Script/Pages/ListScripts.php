<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\Script\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Video\Script;

class ListScripts extends BaseListResources
{
    protected static string $resource = Script::class;
}
