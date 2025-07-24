<?php

declare(strict_types=1);

namespace App\Events\Base\Admin;

use App\Constants\Config\ServiceConstants;
use App\Events\Base\BaseCreatedEvent;
use Illuminate\Support\Facades\Config;

/**
 * Class AdminCreatedEvent.
 *
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends BaseCreatedEvent<TModel>
 */
abstract class AdminCreatedEvent extends BaseCreatedEvent
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
