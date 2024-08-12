<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Admin;

use App\Filament\Resources\Admin\Exception;
use App\Filament\Widgets\BaseTableWidget;
use Filament\Tables\Table;

/**
 * Class ExceptionsTableWidget.
 */
class ExceptionsTableWidget extends BaseTableWidget
{
    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function table(Table $table): Table
    {
        return Exception::table($table)
            ->heading(__('filament-exceptions::filament-exceptions.labels.model_plural'));
    }
}
