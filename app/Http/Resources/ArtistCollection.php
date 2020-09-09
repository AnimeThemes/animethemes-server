<?php

namespace App\Http\Resources;

class ArtistCollection extends BaseCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'App\Http\Resources\ArtistResource';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'artists';
}
