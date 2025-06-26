<?php

declare(strict_types=1);

namespace App\Filament\Resources\Admin\Dump\Pages;

use App\Filament\Resources\Admin\Dump;
use App\Filament\Resources\Base\BaseManageResources;

/**
 * Class ManageDumps.
 */
class ManageDumps extends BaseManageResources
{
    protected static string $resource = Dump::class;
}
