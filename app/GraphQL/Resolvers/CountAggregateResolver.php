<?php

declare(strict_types=1);

namespace App\GraphQL\Resolvers;

use GraphQL\Language\AST\ArgumentNode;
use GraphQL\Language\AST\DirectiveNode;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * Class CountAggregateResolver.
 */
class CountAggregateResolver
{
    /**
     * Resolve count field with aggregates.
     *
     * @param  Model  $aggregatable
     * @param  array  $args
     * @param  GraphQLContext  $context
     * @param  ResolveInfo  $resolveInfo
     * @return mixed
     */
    public function __invoke(Model $aggregatable, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): mixed
    {
        $parentType = $resolveInfo->parentType->name;
        $fieldName = $resolveInfo->fieldName;

        /** @var ObjectType $objectType */
        $objectType = $resolveInfo->schema->getType($parentType);

        $fieldDefinition = $objectType->getField($fieldName);

        $astNode = $fieldDefinition->astNode;

        $relation = null;

        if ($astNode && property_exists($astNode, 'directives')) {
            foreach ($astNode->directives as $directive) {
                /** @var DirectiveNode $directive */
                if ($directive->name->value === 'with') {
                    foreach ($directive->arguments as $arg) {
                        /** @var ArgumentNode $arg */
                        if ($arg->name->value === 'relation' && $arg->value instanceof StringValueNode) {
                            $relation = $arg->value->value;
                            break 2;
                        }
                    }
                }
            }
        }

        if (is_null($relation) || !method_exists($aggregatable, $relation)) {
            throw new InvalidArgumentException("Relation {$relation} does not exist on model " . get_class($aggregatable));
        }

        /** @var Model|null $aggregate */
        $aggregate = $aggregatable->{$relation};

        return (int) $aggregate?->value;
    }
}
