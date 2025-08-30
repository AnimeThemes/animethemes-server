<?php

declare(strict_types=1);

namespace App\Concerns\Discord;

use App\Discord\DiscordEmbedField;

trait HasDiscordEmbedFields
{
    /**
     * @var DiscordEmbedField[]
     */
    protected array $embedFields = [];

    protected function addEmbedField(DiscordEmbedField $embedField): void
    {
        $this->embedFields[] = $embedField;
    }

    /**
     * @return DiscordEmbedField[]
     */
    protected function getEmbedFields(): array
    {
        return $this->embedFields;
    }
}
