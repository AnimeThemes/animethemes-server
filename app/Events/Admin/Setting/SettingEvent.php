<?php

declare(strict_types=1);

namespace App\Events\Admin\Setting;

use App\Models\Admin\Setting;

/**
 * Class SettingEvent.
 */
abstract class SettingEvent
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
}
