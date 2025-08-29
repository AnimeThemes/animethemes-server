<?php

declare(strict_types=1);

namespace App\Filament\Components\Infolist;

use Filament\Infolists\Components\TextEntry as ComponentsTextEntry;
use Filament\Support\Icons\Heroicon;

class TextEntry extends ComponentsTextEntry
{
    public function copyableWithMessage(bool $condition = true): static
    {
        return $this
            ->copyable($condition)
            ->copyMessage(__('filament.actions.base.copied'))
            ->icon(Heroicon::OutlinedClipboard);
    }
}
