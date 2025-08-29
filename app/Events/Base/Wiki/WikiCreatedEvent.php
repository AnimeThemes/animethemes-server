<?php

declare(strict_types=1);

namespace App\Events\Base\Wiki;

use App\Constants\Config\ServiceConstants;
use App\Events\Base\BaseCreatedEvent;
use Illuminate\Support\Facades\Config;

/**
 * @template TModel of \App\Models\BaseModel
 *
 * @extends BaseCreatedEvent<TModel>
 */
abstract class WikiCreatedEvent extends BaseCreatedEvent
{
    /**
     * Get Discord channel the message will be sent to.
     */
    public function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::DB_UPDATES_DISCORD_CHANNEL_QUALIFIED);
    }
}
