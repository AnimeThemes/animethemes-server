<?php

declare(strict_types=1);

namespace App\Filament\Components\Columns;

use App\Filament\Resources\BaseResource;
use App\Models\BaseModel;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class BelongsToColumn.
 */
class BelongsToColumn extends TextColumn
{
    protected ?BaseResource $resource = null;

    /**
     * This should reload after every method.
     *
     * @return void
     */
    public function reload(): void
    {
        $relation = explode('.', $this->getName())[0];

        $this->placeholder('-');
        $this->label($this->resource->getModelLabel());
        $this->tooltip(fn (BelongsToColumn $column) => is_array($column->getState()) ? null : $column->getState());
        $this->weight(FontWeight::SemiBold);
        $this->html();
        $this->url(function (BaseModel|Model $record) use ($relation) {
            foreach (explode('.', $relation) as $element) {
                $record = Arr::get($record, $element);
                if ($record === null) return null;
            }

            $this->formatStateUsing(function () use ($record) {
                $name = $record->getName();
                $nameLimited = Str::limit($name, $this->getCharacterLimit() ?? 100);
                return "<p style='color: rgb(64, 184, 166);'>{$nameLimited}</p>";
            });

            return $this->resource::getUrl('view', ['record' => $record]);
        });
    }

    /**
     * Set the filament resource for the relation.
     *
     * @param  class-string<BaseResource>  $resource
     * @return static
     */
    public function resource(string $resource): static
    {
        $this->resource = new $resource;
        $this->reload();

        return $this;
    }
}
