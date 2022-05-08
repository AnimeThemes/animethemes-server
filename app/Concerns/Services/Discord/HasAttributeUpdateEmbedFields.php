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
    protected function initializeEmbedFields(Model $model): void
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
     * Get model attribute value.
     *
     * @param  Model  $model
     * @param  mixed  $attribute
     * @return mixed
     */
    protected function getAttributeValue(Model $model, mixed $attribute): mixed
    {
        // Hide field from embed by obscuring the values
        if (collect($model->getHidden())->contains($attribute)) {
            return DiscordEmbedField::DEFAULT_FIELD_VALUE;
        }

        return $model->getAttribute($attribute);
    }

    /**
     * Add Embed Fields.
     *
     * @param  Model  $original
     * @param  Model  $changed
     * @param  Collection  $changedAttributes
     * @return void
     */
    protected function addEmbedFields(Model $original, Model $changed, Collection $changedAttributes): void
    {
        foreach ($changedAttributes as $attribute) {
            $this->addEmbedField(new DiscordEmbedField('Attribute', $attribute, true));
            $this->addEmbedField(new DiscordEmbedField('Old', $this->getAttributeValue($original, $attribute), true));
            $this->addEmbedField(new DiscordEmbedField('New', $this->getAttributeValue($changed, $attribute), true));
        }
    }
}
