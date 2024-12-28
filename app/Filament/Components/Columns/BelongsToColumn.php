<?php

declare(strict_types=1);

namespace App\Filament\Components\Columns;

use App\Contracts\Models\Nameable;
use App\Filament\Resources\BaseResource;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Class BelongsToColumn.
 */
class BelongsToColumn extends TextColumn
{
    protected ?BaseResource $resource = null;
    protected bool $shouldUseModelName = false;

    /**
     * Rename the parameter to make it more readable.
     *
     * @param  string  $relation
     * @param  class-string<BaseResource>|null
     * @param  bool|null  $shouldUseModelName
     * @return static
     */
    public static function make(string $relation, ?string $resource = null, ?bool $shouldUseModelName = false): static
    {
        if (!is_string($resource)) {
            throw new RuntimeException('The resource must be specified.');
        }

        $static = app(static::class, ['name' => $relation]);
        $static->resource = new $resource;
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

        $this->label($this->resource->getModelLabel());
        $this->weight(FontWeight::SemiBold);
        $this->html();
        $this->url(function (Model $record) {
            $relation = $this->getName();

            /** @var (Model&Nameable)|null $related */
            $related = $record->$relation;

            if ($related === null) return null;

            $this->formatStateUsing(function () use ($related) {
                $name = $this->shouldUseModelName
                    ? $related->getName()
                    : $this->resource->getRecordTitle($related);

                $nameLimited = Str::limit($name, $this->getCharacterLimit() ?? 100);
                return "<p style='color: rgb(64, 184, 166);'>{$nameLimited}</p>";
            });

            return $this->resource::getUrl('view', ['record' => $related]);
        });
        $this->tooltip(function (Model $record) {
            $relation = $this->getName();

            /** @var (Model&Nameable)|null $related */
            $related = $record->$relation;

            if ($related === null) return null;

            return $this->shouldUseModelName
                ? $related->getName()
                : $this->resource->getRecordTitle($related);
        });

        return $this;
    }
}
