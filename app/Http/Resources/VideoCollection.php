<?php

namespace App\Http\Resources;

class VideoCollection extends BaseCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'App\Http\Resources\VideoResource';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'videos';
}
