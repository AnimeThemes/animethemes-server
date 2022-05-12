<?php

declare(strict_types=1);

namespace App\Events\Base\Admin;

use App\Events\Base\BaseDeletedEvent;
use Illuminate\Support\Facades\Config;

/**
 * Class AdminDeletedEvent.
 *
 * @template TModel of \App\Models\BaseModel
 * @extends BaseDeletedEvent<TModel>
 */
abstract class AdminDeletedEvent extends BaseDeletedEvent
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