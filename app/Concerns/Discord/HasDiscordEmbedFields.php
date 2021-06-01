<?php

declare(strict_types=1);

namespace App\Concerns\Discord;

use App\Discord\DiscordEmbedField;

/**
 * Trait HasDiscordEmbedFields
 * @package App\Concerns\Discord
 */
trait HasDiscordEmbedFields
{
    /**
     * The array of embed fields.
     *
     * @var array
     */
    protected array $embedFields = [];

    /**
     * Add discord embed field.
     *
     * @param DiscordEmbedField $embedField
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
    protected function getEmbedFields(): array
    {
        return $this->embedFields;
    }
}
