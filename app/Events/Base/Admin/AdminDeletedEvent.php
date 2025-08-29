<?php

declare(strict_types=1);

namespace App\Events\Base\Admin;

use App\Constants\Config\ServiceConstants;
use App\Events\Base\BaseDeletedEvent;
use Illuminate\Support\Facades\Config;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends BaseDeletedEvent<TModel>
 */
abstract class AdminDeletedEvent extends BaseDeletedEvent
{
    /**
     * Get Discord channel the message will be sent to.
     */
    public function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::ADMIN_DISCORD_CHANNEL_QUALIFIED);
    }

    /**
     * Determine if the message should be sent.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function shouldSendDiscordMessage(): bool
    {
        return true;
    }
}
