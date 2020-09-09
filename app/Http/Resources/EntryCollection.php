<?php

namespace App\Http\Resources;

class EntryCollection extends BaseCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'App\Http\Resources\EntryResource';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'entries';
}
