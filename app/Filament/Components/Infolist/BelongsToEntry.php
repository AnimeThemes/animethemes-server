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
        throw_unless(is_string($resource), InvalidArgumentException::class, 'The resource must be specified.');

        throw_unless(($resource = new $resource) instanceof BaseResource, InvalidArgumentException::class, 'The resource must instanceof a BaseResource.');

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
            ->url(function (BaseModel|Model $record): ?string {
                $related = $this->getRelated($record);

                if (! $related instanceof Model) {
                    return null;
                }

                return $this->resource::getUrl('view', ['record' => $related]);
            })
            ->formatStateUsing(function (Model $record) {
                $related = $this->getRelated($record);

                if (! $related instanceof Model) {
                    return null;
                }

                return $this->shouldUseModelName
                    ? $related->getName()
                    : $this->resource->getRecordTitle($related);
            })
            ->tooltip(function (Model $record) {
                $related = $this->getRelated($record);

                if (! $related instanceof Model) {
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
