<?php

declare(strict_types=1);

namespace App\Filament\Components\Columns;

use App\Filament\Resources\BaseResource;
use App\Models\BaseModel;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn as ColumnsTextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
     * @param  int|null  $limit
     * @return static
     */
    public function urlToRelated(string $resourceRelated, string $relation, ?bool $shouldUseName = false, ?int $limit = null): static
    {
        return $this
            ->weight(FontWeight::SemiBold)
            ->html()
            ->url(function (BaseModel|Model $record) use ($resourceRelated, $relation, $shouldUseName, $limit) {
                foreach (explode('.', $relation) as $element) {
                    $record = Arr::get($record, $element);
                    if ($record === null) return null;
                }

                $this->formatStateUsing(function ($state) use ($shouldUseName, $record, $limit) {
                    $name = $shouldUseName ? $record->getName() : $state;
                    $nameLimited = Str::limit($name, $limit ?? 100);
                    return "<p style='color: rgb(64, 184, 166);'>{$nameLimited}</p>";
                });

                return (new $resourceRelated)::getUrl('view', ['record' => $record]);
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
            ->copyMessage(__('filament.actions.base.copied'))
            ->icon('heroicon-o-clipboard');
    }
}