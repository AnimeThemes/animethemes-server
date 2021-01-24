<?php

namespace App\Events\ExternalResource;

use App\Models\ExternalResource;

class ExternalResourceEvent
{
    /**
     * The resource that has fired this event.
     *
     * @var \App\Models\ExternalResource
     */
    protected $resource;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\ExternalResource $resource
     * @return void
     */
    public function __construct(ExternalResource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get the resource that has fired this event.
     *
     * @return \App\Models\ExternalResource
     */
    public function getResource()
    {
        return $this->resource;
    }
}
