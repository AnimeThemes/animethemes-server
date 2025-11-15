<?php

declare(strict_types=1);

namespace App\Filament\Tabs;

use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Cache;

abstract class BaseTab extends Tab
{
    abstract public static function getSlug(): string;

    public function count(): mixed
    {
        $count = Cache::flexible("filament_badge_{$this->getSlug()}", [15, 60], fn (): string|int|float|null => $this->getBadge());

        $this->badge($count);

        return $count;
    }

    public function shouldBeHidden(): bool
    {
        if (is_int($count = $this->count())) {
            return $count === 0;
        }

        return false;
    }
}
