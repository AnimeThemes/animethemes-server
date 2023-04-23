<?php

declare(strict_types=1);

namespace App\Events\Admin\Setting;

use App\Constants\Config\ServiceConstants;
use App\Contracts\Events\DiscordMessageEvent;
use App\Models\Admin\Setting;
use Illuminate\Support\Facades\Config;

/**
 * Class SettingEvent.
 */
abstract class SettingEvent implements DiscordMessageEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Setting  $setting
     * @return void
     */
    public function __construct(protected Setting $setting)
    {
    }

    /**
     * Get the setting that has fired this event.
     *
     * @return Setting
     */
    public function getSetting(): Setting
    {
        return $this->setting;
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
        return true;
    }
}
