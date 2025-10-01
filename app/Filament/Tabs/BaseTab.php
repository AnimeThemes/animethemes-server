<?php

declare(strict_types=1);

namespace App\Filament\Tabs;

use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

abstract class BaseTab extends Tab
{
    abstract public static function getSlug(): string;

    public function modifyQuery(Builder $query): Builder
    {
        return Cache::flexible("filament_query_{$this->getSlug()}", [15, 60], fn (): Builder => $this->modifyQuery($query));
    }

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
