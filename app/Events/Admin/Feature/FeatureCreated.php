<?php

declare(strict_types=1);

namespace App\Events\Admin\Feature;

use App\Enums\Discord\EmbedColor;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class FeatureCreated.
 */
class FeatureCreated extends FeatureEvent
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
        $feature = $this->getFeature();

        $embed = array_merge(
            [
                'description' => "Feature '**{$feature->getName()}**' has been created.",
                'color' => EmbedColor::GREEN->value,
            ],
            $this->getUserFooter(),
        );

        return DiscordMessage::create('', $embed);
    }
}
