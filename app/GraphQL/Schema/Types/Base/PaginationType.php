<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types\Base;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type as GraphQLType;
use Illuminate\Pagination\LengthAwarePaginator;
use Rebing\GraphQL\Support\Facades\GraphQL;

class PaginationType extends ObjectType
{
    public function __construct(string $typeName, ?string $customName = null)
    {
        $name = $customName;

        $underlyingType = GraphQL::type($typeName);

        $config = [
            'name' => $name,
            'fields' => $this->getPaginationFields($underlyingType),
        ];

        if (isset($underlyingType->config['model'])) {
            $config['model'] = $underlyingType->{'config'}['model'];
        }

        parent::__construct($config);
    }

    protected function getPaginationFields(GraphQLType $underlyingType): array
    {
        return [
            'data' => [
                'type' => GraphQLType::nonNull(GraphQLType::listOf(GraphQLType::nonNull($underlyingType))),
                'description' => 'List of items on the current page',
                'resolve' => fn (LengthAwarePaginator $data) => $data->getCollection(),
            ],
            'paginationInfo' => [
                'type' => GraphQLType::nonNull(GraphQL::type('PaginationInfo')),
                'description' => 'Pagination information about the list of items.',
                'resolve' => fn (LengthAwarePaginator $data) => $data,
            ],
        ];
    }
}
