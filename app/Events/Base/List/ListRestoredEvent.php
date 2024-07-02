<?php

declare(strict_types=1);

namespace App\Events\Base\List;

use App\Constants\Config\ServiceConstants;
use App\Events\Base\BaseRestoredEvent;
use Illuminate\Support\Facades\Config;

/**
 * Class ListRestoredEvent.
 *
 * @template TModel of \App\Models\BaseModel
 *
 * @extends BaseRestoredEvent<TModel>
 */
abstract class ListRestoredEvent extends BaseRestoredEvent
{
    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::ADMIN_DISCORD_CHANNEL_QUALIFIED);
    }

    /**
     * Determine if the message should be sent.
     *
     * @return bool
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function shouldSendDiscordMessage(): bool
    {
        return true;
    }
}
