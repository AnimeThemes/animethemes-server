<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Api\Query\ReadQuery;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class BaseResource.
 */
abstract class BaseResource extends JsonResource
{
    final public const ATTRIBUTE_ID = 'id';

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(mixed $resource, protected readonly ReadQuery $query)
    {
        parent::__construct($resource);
    }

    /**
     * Determine if field should be included in the response for this resource.
     *
     * @param  string  $field
     * @return bool
     */
    protected function isAllowedField(string $field): bool
    {
        $criteria = $this->query->getFieldCriteria(static::$wrap);

        return $criteria === null || $criteria->isAllowedField($field);
    }
}
