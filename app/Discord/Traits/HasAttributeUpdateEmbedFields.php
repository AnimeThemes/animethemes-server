<?php

namespace App\Discord\Traits;

use App\Discord\Embed\DiscordEmbedField;
use Illuminate\Database\Eloquent\Model;

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

        $changedAttributes = collect($model->getChanges())
            ->forget($model->getCreatedAtColumn())
            ->forget($model->getUpdatedAtColumn())
            ->keys();

        foreach ($changedAttributes as $attribute) {
            $this->addEmbedField(DiscordEmbedField::make('Attribute', $attribute, true));
            $this->addEmbedField(DiscordEmbedField::make('Old', $original->getAttribute($attribute), true));
            $this->addEmbedField(DiscordEmbedField::make('New', $model->getAttribute($attribute), true));
        }
    }
}
