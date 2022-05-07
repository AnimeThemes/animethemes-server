<?php

declare(strict_types=1);

namespace App\Events\Base\Wiki;

use App\Events\Base\BaseCreatedEvent;
use Illuminate\Support\Facades\Config;

/**
 * Class WikiCreatedEvent.
 *
 * @template TModel of \App\Models\BaseModel
 * @extends BaseCreatedEvent<TModel>
 */
abstract class WikiCreatedEvent extends BaseCreatedEvent
{
    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel(): string
    {
        return Config::get('services.discord.db_updates_discord_channel');
    }
}
