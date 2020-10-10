<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class BaseCollection extends ResourceCollection
{
    /**
     * Indicates if all existing request query parameters should be added to pagination links.
     *
     * @var bool
     */
    protected $preserveAllQueryParameters = true;

    /**
     * Sparse field set specified by the client
     *
     * @var \App\JsonApi\FieldSetFilter
     */
    protected $fieldSets;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource, $fieldSets)
    {
        parent::__construct($resource);

        $this->fieldSets = $fieldSets;
    }
}
