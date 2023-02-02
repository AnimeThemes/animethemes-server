<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Base;

use App\Http\Resources\BaseResource;
use App\Scout\Elasticsearch\Api\Field\IntField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

/**
 * Class IdField.
 */
class IdField extends IntField
{
    /**
     * Create a new field instance.
     *
     * @param  Schema  $schema
     * @param  string  $column
     */
    public function __construct(Schema $schema, string $column)
    {
        parent::__construct($schema,BaseResource::ATTRIBUTE_ID, $column);
    }
}
