<?php

namespace App\Discord\Traits;

use App\Discord\Embed\DiscordEmbedField;

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
