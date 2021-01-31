<?php

namespace App\Concerns\Discord;

use App\Discord\DiscordEmbedField;

trait HasDiscordEmbedFields
{
    /**
     * The array of embed fields.
     *
     * @var array
     */
    protected $embedFields = [];

    /**
     * Add discord embed field.
     *
     * @param \App\Discord\DiscordEmbedField $embedField
     * @return void
     */
    protected function addEmbedField(DiscordEmbedField $embedField)
    {
        $this->embedFields[] = $embedField;
    }

    /**
     * Get discord embed fields.
     *
     * @return array
     */
    protected function getEmbedFields()
    {
        return $this->embedFields;
    }
}
