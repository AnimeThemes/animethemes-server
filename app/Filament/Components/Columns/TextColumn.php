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
     * @return static
     */
    public function urlToRelated(string $resourceRelated, string $relation): static
    {
        return $this
            ->weight(FontWeight::SemiBold)
            ->html()
            ->formatStateUsing(fn ($state) => "<p style='color: rgb(64, 184, 166);'>$state</p>")
            ->hiddenOn(BaseRelationManager::class)
            ->url(function (BaseModel $record) use ($resourceRelated, $relation) { 
                foreach (explode('.', $relation) as $element) {
                    $record = $record->$element;
                } 

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