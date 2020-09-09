<?php

namespace App\Http\Resources;

class AnimeCollection extends BaseCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'App\Http\Resources\AnimeResource';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'anime';
}
