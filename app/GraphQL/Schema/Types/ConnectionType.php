<?php

declare(strict_types=1);

namespace App\GraphQL\Schema\Types;

use App\GraphQL\Schema\Types\Base\PaginationInfoType;
use App\GraphQL\Schema\Types\Pivot\PivotType;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as RebingType;

class ConnectionType extends RebingType
{
    protected ?PivotType $pivotType = null;

    public function __construct(protected EdgeType $edgeType)
    {
        GraphQL::addType($edgeType, $edgeType->getName());
    }

    /**
     * @return array<string,mixed>
     */
    public function attributes(): array
    {
        return [
            'name' => $this->getName(),
        ];
    }

    /**
     * The fields of the type.
     *
     * @return array<string, array<string, mixed>>
     */
    public function fields(): array
    {
        return [
            'pageInfo' => [
                'type' => Type::nonNull(GraphQL::type(new PaginationInfoType()->getName())),
                'description' => 'Pagination information about the list of edges.',
                'resolve' => fn (LengthAwarePaginator $paginator): LengthAwarePaginator => $paginator,
            ],
            'edges' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type($this->edgeType->getName())))),
                'description' => "A list of {$this->getNodeTypeName()} edges.",
                'resolve' => fn (LengthAwarePaginator $paginator): Collection => $this->edgesResolver($paginator),
            ],
            'nodes' => [
                'type' => Type::nonNull(Type::listOf(Type::nonNull(GraphQL::type($this->getNodeTypeName())))),
                'description' => "A list of {$this->getNodeTypeName()} resources. Use this if you don\'t care about pivot fields.",
                'resolve' => fn (LengthAwarePaginator $paginator): Collection => new Collection(array_values($paginator->items())),
            ],
        ];
    }

    /**
     * Get the name of the connection.
     * Template: {edgeType - Edge}Connection.
     */
    public function getName(): string
    {
        return Str::of($this->edgeType->getName())
            ->remove('Edge')
            ->append('Connection')
            ->__toString();
    }

    /**
     * Get the name of the node type.
     */
    public function getNodeTypeName(): string
    {
        return Str::remove('Type', class_basename($this->edgeType->getNodeType()));
    }

    /**
     * Resolve the edges field.
     */
    protected function edgesResolver(LengthAwarePaginator $paginator): Collection
    {
        $fields = $this->edgeType->fields();

        $values = new Collection(array_values($paginator->items()));

        return $values->map(function (Model $item) use ($fields): array {
            $edges = [];
            foreach ($fields as $name => $field) {
                $column = Arr::get($field, 'alias');

                $relation = current($item->getRelations());

                if ($name === 'node') {
                    $edges['node'] = $item;
                } else {
                    $edges[$column] = $relation->getAttribute($column);
                }
            }

            return $edges;
        });
    }
}
