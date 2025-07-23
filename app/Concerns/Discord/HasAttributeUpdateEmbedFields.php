<?php

declare(strict_types=1);

namespace App\Concerns\Discord;

use App\Discord\DiscordEmbedField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait HasAttributeUpdateEmbedFields
{
    use HasDiscordEmbedFields;

    /**
     * Initialize embed fields with inline attribute changes.
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
     */
    protected function getAttributeValue(Model $model, mixed $attribute): mixed
    {
        // Hide field from embed by obscuring the values
        if (in_array($attribute, $model->getHidden())) {
            return DiscordEmbedField::DEFAULT_FIELD_VALUE;
        }

        return $model->getAttribute($attribute);
    }

    /**
     * Add Embed Fields.
     *
     * @param  Collection  $changedAttributes
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
