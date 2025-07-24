<?php

declare(strict_types=1);

namespace App\Filament\Components\Columns;

use App\Contracts\Models\Nameable;
use App\Filament\Resources\BaseResource;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InvalidArgumentException;

class BelongsToColumn extends TextColumn
{
    protected BaseResource $resource;
    protected bool $shouldUseModelName = false;

    /**
     * Rename the parameter to make it more readable.
     *
     * @param  string|null  $relation
     * @param  class-string<BaseResource>|null  $resource
     * @param  bool|null  $shouldUseModelName
     * @return static
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

    /**
     * Configure the column.
     *
     * @return static
     */
    public function configure(): static
    {
        parent::configure();

        return $this
            ->label($this->resource->getModelLabel())
            ->weight(FontWeight::SemiBold)
            ->color('related-link')
            ->url(function (Model $record) {
                $relation = $this->getName();

                /** @var (Model&Nameable)|null $related */
                $related = $record->$relation;

                if ($related === null) {
                    return null;
                }

                $this->formatStateUsing(function () use ($related) {
                    $name = $this->shouldUseModelName
                        ? $related->getName()
                        : $this->resource->getRecordTitle($related);

                    $nameLimited = Str::limit($name, $this->getCharacterLimit() ?? 50);

                    return $nameLimited;
                });

                return $this->resource::getUrl('view', ['record' => $related]);
            })
            ->tooltip(function (Model $record) {
                $relation = $this->getName();

                /** @var (Model&Nameable)|null $related */
                $related = $record->$relation;

                if ($related === null) {
                    return null;
                }

                return $this->shouldUseModelName
                    ? $related->getName()
                    : $this->resource->getRecordTitle($related);
            });
    }
}
