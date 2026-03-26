<?php

declare(strict_types=1);

namespace App\Events\Base\Pivot;

use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Enums\Discord\EmbedColor;
use App\Events\BasePivotEvent;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

/**
 * @template TModelRelated of \Illuminate\Database\Eloquent\Model
 * @template TModelForeign of \Illuminate\Database\Eloquent\Model
 *
 * @extends BasePivotEvent<TModelRelated, TModelForeign>
 */
abstract class PivotUpdatedEvent extends BasePivotEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    public function getDiscordMessage(): DiscordMessage
    {
        $embed = array_merge(
            [
                'description' => $this->getDiscordMessageDescription(),
                'fields' => $this->getEmbedFields(),
                'color' => EmbedColor::YELLOW->value,
            ],
            $this->getUserFooter(),
        );

        return DiscordMessage::create('', $embed);
    }

    protected function getDiscordMessageDescription(): string
    {
        $foreign = $this->getForeign();
        $related = $this->getRelated();

        return "{$this->privateLabel($foreign)} '**{$foreign->getName()}**' for {$this->privateLabel($related)} '**{$related->getName()}**' has been updated.";
    }
}
