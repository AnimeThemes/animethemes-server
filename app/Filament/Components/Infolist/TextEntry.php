<?php

declare(strict_types=1);

namespace App\Filament\Components\Infolist;

use App\Filament\Resources\BaseResource;
use App\Models\BaseModel;
use Filament\Infolists\Components\TextEntry as ComponentsTextEntry;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

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
            ->placeholder('-')
            ->weight(FontWeight::SemiBold)
            ->html()
            ->url(function (BaseModel|Model $record) use ($resourceRelated, $relation, $shouldUseName) {
                if (!empty($relation)) {
                    foreach (explode('.', $relation) as $element) {
                        $record = Arr::get($record, $element);
                        if ($record === null) return null;
                    }
                }

                $this->formatStateUsing(function ($state) use ($shouldUseName, $record) {
                    $name = $shouldUseName ? $record->getName() : $state;
                    return "<p style='color: rgb(64, 184, 166);'>{$name}</p>";
                });

                return $resourceRelated::getUrl('view', ['record' => $record]);
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
            ->copyMessage(__('filament.actions.base.copied'))
            ->icon('heroicon-o-clipboard');
    }
}