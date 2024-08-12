<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget;

/**
 * Class BaseTableWidget.
 */
abstract class BaseTableWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';
}
