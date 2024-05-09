<?php

declare(strict_types=1);

namespace App\Concerns\Filament;

/**
 * Trait HasTabs.
 */
trait HasTabs
{
    /**
     * Get the tabs for an array key-mapped.
     * 
     * @param  class-string[]  $tabClasses
     * @return array
     */
    public function toArray(array $tabClasses): array
    {
        $tabs = [];

        foreach ($tabClasses as $class) {
            $tabs[$class::getKey()] = $class::make();
        }

        return $tabs;
    }
}
