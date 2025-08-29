<?php

declare(strict_types=1);

namespace App\Events\Base;

use App\Contracts\Events\DiscordMessageEvent;
use App\Enums\Discord\EmbedColor;
use App\Events\BaseEvent;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends BaseEvent<TModel>
 */
abstract class BaseDeletedEvent extends BaseEvent implements DiscordMessageEvent
{
    use Dispatchable;

    public function getDiscordMessage(): DiscordMessage
    {
        $embed = array_merge(
            [
                'description' => $this->getDiscordMessageDescription(),
                'color' => EmbedColor::RED->value,
            ],
            $this->getUserFooter(),
        );

        return DiscordMessage::create('', $embed);
    }

    public function shouldSendDiscordMessage(): bool
    {
        return true;
    }

    abstract protected function getDiscordMessageDescription(): string;
}
