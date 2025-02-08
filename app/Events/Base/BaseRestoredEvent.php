<?php

declare(strict_types=1);

namespace App\Events\Base;

use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Discord\EmbedColor;
use App\Events\BaseEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class BaseRestoredEvent.
 *
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends BaseEvent<TModel>
 */
abstract class BaseRestoredEvent extends BaseEvent implements DiscordMessageEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $embed = array_merge(
            [
                'description' => $this->getDiscordMessageDescription(),
                'color' => EmbedColor::GREEN->value,
            ],
            $this->getUserFooter(),
        );

        return DiscordMessage::create('', $embed);
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
