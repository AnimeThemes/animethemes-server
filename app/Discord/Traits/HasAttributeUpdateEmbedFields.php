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
        $originalModel = collect($model->getOriginal());

        $changedAttributes = collect($model->getChanges())
            ->forget($model->getCreatedAtColumn())
            ->forget($model->getUpdatedAtColumn());

        foreach ($changedAttributes as $attribute => $value) {
            $this->addEmbedField(DiscordEmbedField::make('Attribute', $attribute, true));
            $this->addEmbedField(DiscordEmbedField::make('Old', $originalModel->get($attribute), true));
            $this->addEmbedField(DiscordEmbedField::make('New', $value, true));
        }
    }
}
