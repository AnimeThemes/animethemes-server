<?php

declare(strict_types=1);

namespace App\Events\Base\Pivot;

use App\Enums\Discord\EmbedColor;
use App\Events\BasePivotEvent;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

/**
 * @template TModelRelated of \Illuminate\Database\Eloquent\Model
 * @template TModelForeign of \Illuminate\Database\Eloquent\Model
 *
 * @extends BasePivotEvent<TModelRelated, TModelForeign>
 */
abstract class PivotDeletedEvent extends BasePivotEvent
{
    use Dispatchable;
    use SerializesModels;

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

    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "{$this->privateLabel($foreign)} '**{$foreign->getName()}**' has been detached from {$this->privateLabel($related)} '**{$related->getName()}**'.";
    }
}
