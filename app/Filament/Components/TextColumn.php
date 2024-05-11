<?php

declare(strict_types=1);

namespace App\Filament\Components;

use App\Filament\Resources\BaseRelationManager;
use App\Models\BaseModel;
use Filament\Tables\Columns\TextColumn as ColumnsTextColumn;

/**
 * Class TextColumn.
 */
class TextColumn extends ColumnsTextColumn
{
    /**
     * Used to relation columns.
     *
     * @param  string  $resourceRelated
     * @param  string  $relation
     * @return static
     */
    public function urlToRelated(string $resourceRelated, string $relation): static
    {
        return $this
            ->color('info')
            ->hiddenOn(BaseRelationManager::class)
            ->url(function (BaseModel $record) use ($resourceRelated, $relation) { 
                foreach (explode('.', $relation) as $element) {
                    $record = $record->$element;
                } 

                return $record !== null ? (new $resourceRelated)::getUrl('edit', ['record' => $record]) : null;
            });
    }
}