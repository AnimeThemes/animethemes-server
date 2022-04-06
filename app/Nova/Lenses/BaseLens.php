<?php

declare(strict_types=1);

namespace App\Nova\Lenses;

use Laravel\Nova\Lenses\Lens;

/**
 * Class BaseLens.
 */
abstract class BaseLens extends Lens
{
    /**
     * Indicates whether the resource should automatically poll for new resources.
     *
     * @var bool
     */
    public static $polling = true;

    /**
     * The interval at which Nova should poll for new resources.
     *
     * @var int
     */
    public static $pollingInterval = 60;

    /**
     * Indicates whether to show the polling toggle button inside Nova.
     *
     * @var bool
     */
    public static $showPollingToggle = true;
}
