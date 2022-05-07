<?php

declare(strict_types=1);

namespace App\Events\Base\Wiki;

use App\Events\Base\BaseRestoredEvent;
use Illuminate\Support\Facades\Config;

/**
 * Class WikiRestoredEvent.
 *
 * @template TModel of \App\Models\BaseModel
 * @extends BaseRestoredEvent<TModel>
 */
abstract class WikiRestoredEvent extends BaseRestoredEvent
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
