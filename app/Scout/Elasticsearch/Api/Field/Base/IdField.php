<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Base;

use App\Http\Resources\BaseResource;
use App\Scout\Elasticsearch\Api\Field\IntField;

/**
 * Class IdField.
 */
class IdField extends IntField
{
    /**
     * Create a new field instance.
     *
     * @param  string  $column
     */
    public function __construct(string $column)
    {
        parent::__construct(BaseResource::ATTRIBUTE_ID, $column);
    }
}
