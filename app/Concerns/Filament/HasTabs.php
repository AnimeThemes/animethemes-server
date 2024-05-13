<?php

declare(strict_types=1);

namespace App\Concerns\Filament;

use App\Filament\Tabs\BaseTab;

/**
 * Trait HasTabs.
 */
trait HasTabs
{
    /**
     * Get the tabs for an array key-mapped.
     * 
     * @param  class-string<BaseTab>[]  $tabClasses
     * @return array
     */
    public function toArray(array $tabClasses): array
    {
        $tabs = [];

        foreach ($tabClasses as $class) {
            if ((new $class)->hidden()) continue;
            $tabs[$class::getKey()] = $class::make();
        }

        return $tabs;
    }
}
