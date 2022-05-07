<?php

declare(strict_types=1);

namespace App\Events\Base\Admin;

use App\Events\Base\BaseCreatedEvent;
use Illuminate\Support\Facades\Config;

/**
 * Class AdminCreatedEvent.
 *
 * @template TModel of \App\Models\BaseModel
 * @extends BaseCreatedEvent<TModel>
 */
abstract class AdminCreatedEvent extends BaseCreatedEvent
{
    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel(): string
    {
        return Config::get('services.discord.admin_discord_channel');
    }
}
