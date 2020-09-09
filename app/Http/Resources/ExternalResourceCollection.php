<?php

namespace App\Http\Resources;

class ExternalResourceCollection extends BaseCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'App\Http\Resources\ExternalResourceResource';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'resources';
}
