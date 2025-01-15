<?php

declare(strict_types=1);

namespace App\Filament\Components\Infolist;

use Filament\Infolists\Components\TextEntry as ComponentsTextEntry;

/**
 * Class TextEntry.
 */
class TextEntry extends ComponentsTextEntry
{
    /**
     * Make the entry copyable.
     *
     * @param  bool  $condition
     * @return static
     */
    public function copyableWithMessage(bool $condition = true): static
    {
        return $this
            ->copyable($condition)
            ->copyMessage(__('filament.actions.base.copied'))
            ->icon(__('filament-icons.actions.base.copied'));
    }
}