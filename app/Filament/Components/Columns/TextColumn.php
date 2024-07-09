<?php

declare(strict_types=1);

namespace App\Filament\Components\Columns;

use Filament\Tables\Columns\TextColumn as ColumnsTextColumn;

/**
 * Class TextColumn.
 */
class TextColumn extends ColumnsTextColumn
{
    /**
     * Make the column copyable.
     *
     * @param  bool  $condition
     * @return static
     */
    public function copyableWithMessage(bool $condition = true): static
    {
        return $this
            ->copyable($condition)
            ->copyMessage(__('filament.actions.base.copied'))
            ->icon('heroicon-o-clipboard');
    }
}