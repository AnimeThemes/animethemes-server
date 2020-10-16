<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseResource extends JsonResource
{
    /**
     * Sparse field set specified by the client.
     *
     * @var \App\JsonApi\QueryParser
     */
    protected $parser;

    /**
     * The name of the resource in the field set mapping.
     *
     * @var string
     */
    public static $resourceType;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource, $parser)
    {
        parent::__construct($resource);

        $this->parser = $parser;
    }

    /**
     * Determine if field should be included in the response for this resource type.
     *
     * @param string $field
     * @return bool
     */
    protected function isAllowedField($field)
    {
        return $this->parser->isAllowedField(static::$resourceType, $field);
    }
}
