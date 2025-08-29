<?php

declare(strict_types=1);

namespace App\Filament\Components\Columns;

use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn as ColumnsTextColumn;

class TextColumn extends ColumnsTextColumn
{
    /**
     * Make the column copyable.
     */
    public function copyableWithMessage(bool $condition = true): static
    {
        return $this
            ->copyable($condition)
            ->copyMessage(__('filament.actions.base.copied'))
            ->icon(Heroicon::OutlinedClipboard);
    }
}
