<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget;

abstract class BaseTableWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';
}
