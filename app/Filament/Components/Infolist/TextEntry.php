<?php

declare(strict_types=1);

namespace App\Filament\Components\Infolist;

use App\Filament\Resources\BaseResource;
use App\Models\BaseModel;
use Filament\Infolists\Components\TextEntry as ComponentsTextEntry;
use Filament\Support\Enums\FontWeight;

/**
 * Class TextEntry.
 */
class TextEntry extends ComponentsTextEntry
{
    /**
     * Used for entry relationships.
     *
     * @param  class-string<BaseResource>|string  $resourceRelated
     * @param  string  $relation
     * @param  bool|null  $shouldUseName
     * @return static
     */
    public function urlToRelated(string $resourceRelated, string $relation, ?bool $shouldUseName = false): static
    {
        return $this
            ->weight(FontWeight::SemiBold)
            ->html()
            ->url(function (BaseModel $record) use ($resourceRelated, $relation, $shouldUseName) { 
                foreach (explode('.', $relation) as $element) {
                    $record = $record->$element;
                } 

                $this->formatStateUsing(function ($state) use ($shouldUseName, $record) {
                    $name = $shouldUseName ? $record->getName() : $state;
                    return "<p style='color: rgb(64, 184, 166);'>{$name}</p>";
                });

                return $record !== null ? (new $resourceRelated)::getUrl('edit', ['record' => $record]) : null;
            });
    }

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
            ->copyMessage(__('filament.actions.base.copied'));
    }
}