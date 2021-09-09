<?php

declare(strict_types=1);

namespace App\Concerns\Services\Discord;

use App\Services\Discord\DiscordEmbedField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Trait HasAttributeUpdateEmbedFields.
 */
trait HasAttributeUpdateEmbedFields
{
    use HasDiscordEmbedFields;

    /**
     * Initialize embed fields with inline attribute changes.
     *
     * @param  Model  $model
     * @return void
     */
    protected function initializeEmbedFields(Model $model)
    {
        $original = $model->newInstance($model->getOriginal());

        $changedAttributes = $this->getChangedAttributes($model);

        $this->addEmbedFields($original, $model, $changedAttributes);
    }

    /**
     * Get changed attributes.
     *
     * @param  Model  $model
     * @return Collection
     */
    protected function getChangedAttributes(Model $model): Collection
    {
        return collect($model->getChanges())
            ->forget($model->getCreatedAtColumn())
            ->forget($model->getUpdatedAtColumn())
            ->keys();
    }

    /**
     * Add Embed Fields.
     *
     * @param  Model  $original
     * @param  Model  $changed
     * @param  Collection  $changedAttributes
     * @return void
     */
    protected function addEmbedFields(Model $original, Model $changed, Collection $changedAttributes)
    {
        foreach ($changedAttributes as $attribute) {
            $this->addEmbedField(DiscordEmbedField::make('Attribute', $attribute, true));
            $this->addEmbedField(DiscordEmbedField::make('Old', $original->getAttribute($attribute), true));
            $this->addEmbedField(DiscordEmbedField::make('New', $changed->getAttribute($attribute), true));
        }
    }
}
