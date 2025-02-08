<?php

declare(strict_types=1);

namespace App\Events;

use App\Constants\Config\ServiceConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Models\Auth\User;
use App\Models\BaseModel;
use Illuminate\Support\Facades\Auth;
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
     * Get the user that has fired this event.
     *
     * @return User|null
     */
    protected function getAuthenticatedUser(): ?User
    {
        return Auth::user();
    }

    /**
     * Get the user info for the footer.
     *
     * @return array
     */
    protected function getUserFooter(): array
    {
        if (is_null($this->getAuthenticatedUser())) {
            return [];
        }

        return [
            'footer' => [
                'text' => $this->getAuthenticatedUser()->getName(),
                'icon_url' => $this->getAuthenticatedUser()->getFilamentAvatarUrl(),
            ]
        ];
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::DB_UPDATES_DISCORD_CHANNEL_QUALIFIED);
    }

    /**
     * Determine if the message should be sent.
     *
     * @return bool
     */
    public function shouldSendDiscordMessage(): bool
    {
        return true;
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    abstract protected function getDiscordMessageDescription(): string;
}
