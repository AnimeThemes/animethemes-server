<?php

declare(strict_types=1);

namespace App\Events\Wiki\Studio;

use App\Models\Wiki\Studio;

/**
 * Class StudioEvent.
 */
abstract class StudioEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Studio  $studio
     * @return void
     */
    public function __construct(protected Studio $studio)
    {
    }

    /**
     * Get the studio that has fired this event.
     *
     * @return Studio
     */
    public function getStudio(): Studio
    {
        return $this->studio;
    }
}
