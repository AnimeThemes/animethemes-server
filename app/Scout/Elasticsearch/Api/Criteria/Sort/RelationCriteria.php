<?php

declare(strict_types=1);

namespace App\Scout\Elasticsearch\Api\Criteria\Sort;

use App\Http\Api\Sort\Sort;
use Illuminate\Support\Str;

class RelationCriteria extends FieldCriteria
{
    /**
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function sort(Sort $sort): array
    {
        return [
            $sort->getColumn() => [
                'order' => $this->direction->value,
                'nested' => [
                    'path' => Str::beforeLast($sort->getColumn(), '.'),
                ],
            ],
        ];
    }
}
