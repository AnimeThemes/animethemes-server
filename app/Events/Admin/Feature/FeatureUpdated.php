<?php

declare(strict_types=1);

namespace App\Events\Admin\Feature;

use App\Concerns\Discord\HasAttributeUpdateEmbedFields;
use App\Enums\Discord\EmbedColor;
use App\Models\Admin\Feature;
use Illuminate\Foundation\Events\Dispatchable;
use NotificationChannels\Discord\DiscordMessage;

/**
 * Class FeatureUpdated.
 */
class FeatureUpdated extends FeatureEvent
{
    use Dispatchable;
    use HasAttributeUpdateEmbedFields;

    /**
     * Create a new event instance.
     *
     * @param  Feature  $feature
     * @return void
     */
    public function __construct(Feature $feature)
    {
        parent::__construct($feature);
        $this->initializeEmbedFields($feature);
    }

    /**
     * Get Discord message payload.
     *
     * @return DiscordMessage
     */
    public function getDiscordMessage(): DiscordMessage
    {
        $feature = $this->getFeature();

        return DiscordMessage::create('', [
            'description' => "Feature '**{$feature->getName()}**' has been updated.",
            'fields' => $this->getEmbedFields(),
            'color' => EmbedColor::YELLOW->value,
        ]);
    }
}
