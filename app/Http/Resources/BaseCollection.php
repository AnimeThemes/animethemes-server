<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class BaseCollection extends ResourceCollection
{
    /**
     * Sparse field set specified by the client.
     *
     * @var \App\JsonApi\QueryParser
     */
    protected $parser;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param \App\JsonApi\QueryParser $parser
     * @return void
     */
    public function __construct($resource, $parser)
    {
        parent::__construct($resource);

        $this->parser = $parser;
    }
}
