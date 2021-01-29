<?php

namespace App\Concerns\Discord;

use App\Discord\DiscordEmbedField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait HasAttributeUpdateEmbedFields
{
    use HasDiscordEmbedFields;

    /**
     * Initialize embed fields with inline attribute changes.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
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
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Support\Collection
     */
    protected function getChangedAttributes(Model $model)
    {
        return collect($model->getChanges())
            ->forget($model->getCreatedAtColumn())
            ->forget($model->getUpdatedAtColumn())
            ->keys();
    }

    /**
     * Add Embed Fields.
     *
     * @param \Illuminate\Database\Eloquent\Model $original
     * @param \Illuminate\Database\Eloquent\Model $original
     * @param \Illuminate\Support\Collection $changedAttributes
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
