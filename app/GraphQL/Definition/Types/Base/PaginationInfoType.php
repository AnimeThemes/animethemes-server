<?php

declare(strict_types=1);

namespace App\GraphQL\Definition\Types\Base;

use App\GraphQL\Definition\Types\BaseType;
use GraphQL\Type\Definition\Type as GraphQLType;
use Illuminate\Pagination\LengthAwarePaginator;

class PaginationInfoType extends BaseType
{
    protected $attributes = [
        'name' => 'PaginationInfo',
    ];

    public function fields(): array
    {
        return [
            'count' => [
                'type' => GraphQLType::nonNull(GraphQLType::int()),
                'description' => 'Number of items in the current page.',
                'resolve' => fn (LengthAwarePaginator $data) => $data->count(),
                'selectable' => false,
            ],
            'total' => [
                'type' => GraphQLType::nonNull(GraphQLType::int()),
                'description' => 'Number of total items selected by the query',
                'resolve' => fn (LengthAwarePaginator $data) => $data->total(),
                'selectable' => false,
            ],
            'perPage' => [
                'type' => GraphQLType::nonNull(GraphQLType::int()),
                'description' => 'Number of items returned per page',
                'resolve' => fn (LengthAwarePaginator $data) => $data->perPage(),
                'selectable' => false,
            ],
            'currentPage' => [
                'type' => GraphQLType::nonNull(GraphQLType::int()),
                'description' => 'Current page of the cursor',
                'resolve' => fn (LengthAwarePaginator $data) => $data->currentPage(),
                'selectable' => false,
            ],
            'from' => [
                'type' => GraphQLType::int(),
                'description' => 'Number of the first item returned',
                'resolve' => fn (LengthAwarePaginator $data) => $data->firstItem(),
                'selectable' => false,
            ],
            'to' => [
                'type' => GraphQLType::int(),
                'description' => 'Number of the last item returned',
                'resolve' => fn (LengthAwarePaginator $data) => $data->lastItem(),
                'selectable' => false,
            ],
            'lastPage' => [
                'type' => GraphQLType::nonNull(GraphQLType::int()),
                'description' => 'The last page (number of pages)',
                'resolve' => fn (LengthAwarePaginator $data) => $data->lastPage(),
                'selectable' => false,
            ],
            'hasMorePages' => [
                'type' => GraphQLType::nonNull(GraphQLType::boolean()),
                'description' => 'Determines if cursor has more pages after the current page',
                'resolve' => fn (LengthAwarePaginator $data) => $data->hasMorePages(),
                'selectable' => false,
            ],
        ];
    }
}
