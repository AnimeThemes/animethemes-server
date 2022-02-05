<?php

declare(strict_types=1);

namespace App\Concerns\Services\Discord;

use App\Services\Discord\DiscordEmbedField;

/**
 * Trait HasDiscordEmbedFields.
 */
trait HasDiscordEmbedFields
{
    /**
     * The array of embed fields.
     *
     * @var DiscordEmbedField[]
     */
    protected array $embedFields = [];

    /**
     * Add discord embed field.
     *
     * @param  DiscordEmbedField  $embedField
     * @return void
     */
    protected function addEmbedField(DiscordEmbedField $embedField): void
    {
        $this->embedFields[] = $embedField;
    }

    /**
     * Get discord embed fields.
     *
     * @return DiscordEmbedField[]
     */
    protected function getEmbedFields(): array
    {
        return $this->embedFields;
    }
}
