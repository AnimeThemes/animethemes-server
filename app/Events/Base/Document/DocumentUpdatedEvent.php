<?php

declare(strict_types=1);

namespace App\Events\Base\Document;

use App\Constants\Config\ServiceConstants;
use App\Events\Base\BaseUpdatedEvent;
use Illuminate\Support\Facades\Config;

/**
 * @template TModel of \App\Models\BaseModel
 *
 * @extends BaseUpdatedEvent<TModel>
 */
abstract class DocumentUpdatedEvent extends BaseUpdatedEvent
{
    public function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::ADMIN_DISCORD_CHANNEL_QUALIFIED);
    }
}
