<?php

namespace App\Events;

use Illuminate\Database\Eloquent\Model;

trait HasDiscordEmbedFields
{
    /**
     * TODO: still bad
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    protected static function initializeEmbedFields(Model $model)
    {
        $embedFields = [];
        $originalModel = collect($model->getOriginal());

        $changedAttributes = collect($model->getChanges())
            ->forget($model->getCreatedAtColumn())
            ->forget($model->getUpdatedAtColumn());

        foreach ($changedAttributes as $attribute => $value) {
            $embedFields[] = DiscordEmbedField::make('Attribute', $attribute, true);
            $embedFields[] = DiscordEmbedField::make('Old', $originalModel->get($attribute), true);
            $embedFields[] = DiscordEmbedField::make('New', $value, true);
        }

        return $embedFields;
    }
}
