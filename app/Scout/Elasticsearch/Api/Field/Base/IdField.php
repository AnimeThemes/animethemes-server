<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Field\Base;

use App\Http\Resources\BaseJsonResource;
use App\Scout\Elasticsearch\Api\Field\IntField;
use App\Scout\Elasticsearch\Api\Schema\Schema;

class IdField extends IntField
{
    public function __construct(Schema $schema, string $column)
    {
        parent::__construct($schema, BaseJsonResource::ATTRIBUTE_ID, $column);
    }
}
