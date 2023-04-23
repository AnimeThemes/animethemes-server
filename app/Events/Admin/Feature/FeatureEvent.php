<?php

declare(strict_types=1);

namespace App\Events\Admin\Feature;

use App\Constants\Config\ServiceConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Models\Admin\Feature;
use Illuminate\Support\Facades\Config;

/**
 * Class FeatureEvent.
 */
abstract class FeatureEvent implements DiscordMessageEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Feature  $feature
     * @return void
     */
    public function __construct(protected Feature $feature)
    {
    }

    /**
     * Get the feature that has fired this event.
     *
     * @return Feature
     */
    public function getFeature(): Feature
    {
        return $this->feature;
    }

    /**
     * Get Discord channel the message will be sent to.
     *
     * @return string
     */
    public function getDiscordChannel(): string
    {
        return Config::get(ServiceConstants::ADMIN_DISCORD_CHANNEL_QUALIFIED);
    }

    /**
     * Determine if the message should be sent.
     *
     * @return bool
     */
    public function shouldSendDiscordMessage(): bool
    {
        return $this->feature->isNullScope();
    }
}
