<?php

declare(strict_types=1);

namespace App\Filament\Components\Infolist;

use App\Contracts\Models\Nameable;
use App\Filament\Resources\BaseResource;
use App\Models\BaseModel;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class BelongsToEntry extends TextEntry
{
    protected BaseResource $resource;
    protected bool $shouldUseModelName = false;

    /**
     * Rename the parameter to make it more readable.
     *
     * @param  class-string<BaseResource>|null  $resource
     */
    public static function make(?string $relation = null, ?string $resource = null, ?bool $shouldUseModelName = false): static
    {
        if (! is_string($resource)) {
            throw new InvalidArgumentException('The resource must be specified.');
        }

        if (! (($resource = new $resource) instanceof BaseResource)) {
            throw new InvalidArgumentException('The resource must instanceof a BaseResource.');
        }

        $static = app(static::class, ['name' => $relation]);
        $static->resource = $resource;
        $static->shouldUseModelName = $shouldUseModelName;
        $static->configure();

        return $static;
    }

    public function configure(): static
    {
        return $this
            ->label($this->resource->getModelLabel())
            ->weight(FontWeight::SemiBold)
            ->color('related-link')
            ->placeholder('-')
            ->url(function (BaseModel|Model $record) {
                $related = $this->getRelated($record);

                if ($related === null) {
                    return null;
                }

                return $this->resource::getUrl('view', ['record' => $related]);
            })
            ->formatStateUsing(function (Model $record) {
                $related = $this->getRelated($record);

                if ($related === null) {
                    return null;
                }

                $name = $this->shouldUseModelName
                    ? $related->getName()
                    : $this->resource->getRecordTitle($related);

                return $name;
            })
            ->tooltip(function (Model $record) {
                $related = $this->getRelated($record);

                if ($related === null) {
                    return null;
                }

                return $this->shouldUseModelName
                    ? $related->getName()
                    : $this->resource->getRecordTitle($related);
            });
    }

    /**
     * @return (Model&Nameable)|null
     */
    private function getRelated(?Model $record): ?Model
    {
        $relation = $this->getName();

        foreach (explode('.', $relation) as $relationPart) {
            $record = $record->getRelation($relationPart);
        }

        return $record;
    }
}
