<?php

declare(strict_types=1);

namespace App\Events\ExternalResource;

use App\Models\ExternalResource;

/**
 * Class ExternalResourceEvent
 * @package App\Events\ExternalResource
 */
class ExternalResourceEvent
{
    /**
     * The resource that has fired this event.
     *
     * @var ExternalResource
     */
    protected ExternalResource $resource;

    /**
     * Create a new event instance.
     *
     * @param ExternalResource $resource
     * @return void
     */
    public function __construct(ExternalResource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get the resource that has fired this event.
     *
     * @return ExternalResource
     */
    public function getResource(): ExternalResource
    {
        return $this->resource;
    }
}
