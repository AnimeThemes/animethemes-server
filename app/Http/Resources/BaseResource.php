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

    /**
     * Set the parser.
     *
     * @param \App\JsonApi\QueryParser $parser
     * @return static
     */
    public function parser($parser)
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * Determine if field should be included in the response for this resource.
     *
     * @param string $field
     * @return bool
     */
    protected function isAllowedField($field)
    {
        return $this->parser->isAllowedField(static::$wrap, $field);
    }
}
