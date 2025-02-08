<?php

declare(strict_types=1);

namespace App\Events;

use App\Constants\Config\ServiceConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Contracts\Models\Nameable;
use App\Models\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * Class BasePivotEvent.
 *
 * @template TModelRelated of \Illuminate\Database\Eloquent\Model
 * @template TModelForeign of \Illuminate\Database\Eloquent\Model
 */
abstract class BasePivotEvent implements DiscordMessageEvent
{
    /**
     * The user that has fired this event.
     *
     * @var User|null
     */
    protected ?User $authenticatedUser;

    /**
     * Create a new event instance.
     *
     * @param  TModelRelated&Nameable  $related
     * @param  TModelForeign&Nameable  $foreign
     */
    public function __construct(protected Model&Nameable $related, protected Model&Nameable $foreign)
    {
        $this->authenticatedUser = Auth::user();
    }

    /**
     * Get the related model.
     *
     * @return TModelRelated&Nameable
     */
    public function getRelated(): Model&Nameable
    {
        return $this->related;
    }

    /**
     * Get the foreign model.
     *
     * @return TModelForeign&Nameable
     */
    public function getForeign(): Model&Nameable
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
        return $this->authenticatedUser;
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
