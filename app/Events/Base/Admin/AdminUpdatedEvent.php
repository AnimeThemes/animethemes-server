<?php

declare(strict_types=1);

namespace App\Events\Base\Admin;

use App\Events\Base\BaseUpdatedEvent;
use Illuminate\Support\Facades\Config;

/**
 * Class AdminUpdatedEvent.
 *
 * @template TModel of \App\Models\BaseModel
 * @extends BaseUpdatedEvent<TModel>
 */
abstract class AdminUpdatedEvent extends BaseUpdatedEvent
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