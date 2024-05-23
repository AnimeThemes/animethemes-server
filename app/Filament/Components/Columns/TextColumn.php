<?php

declare(strict_types=1);

namespace App\Filament\Components\Columns;

use App\Filament\Resources\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Models\BaseModel;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn as ColumnsTextColumn;

/**
 * Class TextColumn.
 */
class TextColumn extends ColumnsTextColumn
{
    /**
     * Used for column relationships.
     *
     * @param  class-string<BaseResource>  $resourceRelated
     * @param  string  $relation
     * @param  bool|null  $shouldUseName
     * @return static
     */
    public function urlToRelated(string $resourceRelated, string $relation, ?bool $shouldUseName = false): static
    {
        return $this
            ->weight(FontWeight::SemiBold)
            ->html()
            ->hiddenOn(BaseRelationManager::class)
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
     * Make the column copyable.
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