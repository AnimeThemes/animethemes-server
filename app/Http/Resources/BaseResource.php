<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class BaseResource extends JsonResource
{
    /**
     * Sparse field set specified by the client
     *
     * @var \App\JsonApi\FieldSetFilter
     */
    protected $fieldSets;

    /**
     * The name of the resource in the field set mapping
     *
     * @var string
     */
    protected static $resourceType;

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

    /**
     * Determine if field should be included in the response for this resource type
     *
     * @param string $field
     * @return boolean
     */
    protected function isAllowedField($field)
    {
        return $this->fieldSets->isAllowedField(static::$resourceType, $field);
    }
}
