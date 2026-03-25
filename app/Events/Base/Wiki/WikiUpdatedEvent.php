<?php

declare(strict_types=1);

namespace App\Events\Base\Wiki;

use App\Constants\Config\ServiceConstants;
use App\Events\Base\BaseUpdatedEvent;
use Illuminate\Support\Facades\Config;

/**
 * @template TModel of \App\Models\BaseModel
 *
 * @extends BaseUpdatedEvent<TModel>
 */
abstract class WikiUpdatedEvent extends BaseUpdatedEvent
{
    public function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::DB_UPDATES_DISCORD_CHANNEL_QUALIFIED);
    }

    protected function getDiscordMessageDescription(): string
    {
        return "{$this->privateLabel($this->getModel())} '**{$this->getModel()->getName()}**' has been updated.";
    }
}
