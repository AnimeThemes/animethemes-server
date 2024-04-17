<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Dump\Pages;

use App\Filament\Resources\Base\BaseCreateResource;
use App\Filament\Resources\Admin\Dump;

/**
 * Class CreateDump.
 */
class CreateDump extends BaseCreateResource
{
    protected static string $resource = Dump::class;
}
