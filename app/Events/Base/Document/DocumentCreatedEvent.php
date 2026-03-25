<?php

declare(strict_types=1);

namespace App\Events\Base\Document;

use App\Constants\Config\ServiceConstants;
use App\Events\Base\BaseCreatedEvent;
use Illuminate\Support\Facades\Config;

/**
 * @template TModel of \App\Models\BaseModel
 *
 * @extends BaseCreatedEvent<TModel>
 */
abstract class DocumentCreatedEvent extends BaseCreatedEvent
{
    public function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::ADMIN_DISCORD_CHANNEL_QUALIFIED);
    }

    protected function getDiscordMessageDescription(): string
    {
        return "{$this->privateLabel($this->getModel())} '**{$this->getModel()->getName()}**' has been created.";
    }
}
