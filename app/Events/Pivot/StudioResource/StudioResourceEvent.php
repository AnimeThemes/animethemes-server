<?php

declare(strict_types=1);

namespace App\Events\Pivot\StudioResource;

use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\StudioResource;

/**
 * Class StudioResourceEvent.
 */
abstract class StudioResourceEvent
{
    /**
     * The studio that this studio resource belongs to.
     *
     * @var Studio
     */
    protected Studio $studio;

    /**
     * The resource that this studio resource belongs to.
     *
     * @var ExternalResource
     */
    protected ExternalResource $resource;

    /**
     * Create a new event instance.
     *
     * @param  StudioResource  $studioResource
     * @return void
     */
    public function __construct(StudioResource $studioResource)
    {
        $this->studio = $studioResource->studio;
        $this->resource = $studioResource->resource;
    }

    /**
     * Get the studio that this studio resource belongs to.
     *
     * @return Studio
     */
    public function getStudio(): Studio
    {
        return $this->studio;
    }

    /**
     * Get the resource that this studio resource belongs to.
     */
    public function getResource(): ExternalResource
    {
        return $this->resource;
    }
}
