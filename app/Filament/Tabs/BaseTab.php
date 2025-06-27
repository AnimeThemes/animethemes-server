<?php

declare(strict_types=1);

namespace App\Filament\Tabs;

use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

/**
 * Class BaseTab.
 */
abstract class BaseTab extends Tab
{
    /**
     * Get the key for the tab.
     *
     * @return string
     */
    abstract public static function getKey(): string;

    /**
     * Get the displayable name of the tab.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->getLabel();
    }

    /**
     * The query used to refine the models for the tab.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return Cache::flexible("filament_query_{$this->getKey()}", [15, 60], function () use ($query) {
            return $this->modifyQuery($query);
        });
    }

    /**
     * Get the badge for the tab.
     *
     * @return mixed
     */
    public function count(): mixed
    {
        $count = Cache::flexible("filament_badge_{$this->getKey()}", [15, 60], function () {
            return $this->getBadge();
        });

        $this->badge($count);

        return $count;
    }

    /**
     * Determine if the tab should be hidden.
     *
     * @return bool
     */
    public function shouldBeHidden(): bool
    {
        if (is_int($count = $this->count())) {
            return $count === 0;
        }

        return false;
    }
}
