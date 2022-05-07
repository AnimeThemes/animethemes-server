<?php

declare(strict_types=1);

namespace App\Events;

use App\Contracts\Events\DiscordMessageEvent;
use App\Models\BaseModel;
use Illuminate\Support\Facades\Config;

/**
 * Class BasePivotEvent.
 *
 * @template TModelRelated of \App\Models\BaseModel
 * @template TModelForeign of \App\Models\BaseModel
 */
abstract class BasePivotEvent implements DiscordMessageEvent
{
    /**
     * Create a new event instance.
     *
     * @param  TModelRelated  $related
     * @param  TModelForeign  $foreign
     */
    public function __construct(protected BaseModel $related, protected BaseModel $foreign)
    {
    }

    /**
     * Get the related model.
     *
     * @return TModelRelated
     */
    public function getRelated(): BaseModel
    {
        return $this->related;
    }

    /**
     * Get the foreign model.
     *
     * @return TModelForeign
     */
    public function getForeign(): BaseModel
    {
        return $this->foreign;
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel(): string
    {
        return Config::get('services.discord.db_updates_discord_channel');
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    abstract protected function getDiscordMessageDescription(): string;
}
