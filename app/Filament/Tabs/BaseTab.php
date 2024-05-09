<?php

declare(strict_types=1);

namespace App\Filament\Tabs;

use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class BaseTab.
 */
abstract class BaseTab extends Tab
{
    abstract static public function getKey(): string;

    /**
     * Get the displayable name of the tab.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
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
        return $this->modifyQuery($query);
    }

    /**
     * Get the badge for the tab.
     *
     * @return int
     */
    public function getBadge(): int
    {
        return $this->getBadge();
    }
}
