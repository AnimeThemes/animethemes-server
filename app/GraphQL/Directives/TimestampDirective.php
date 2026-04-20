<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldResolver;

class TimestampDirective extends BaseDirective implements FieldResolver
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
directive @timestamp(attribute: String) on FIELD_DEFINITION
GRAPHQL;
    }

    /**
     * Returns a field resolver function.
     *
     * @return callable(mixed, array<string, mixed>, \Nuwave\Lighthouse\Support\Contracts\GraphQLContext, \Nuwave\Lighthouse\Execution\ResolveInfo): mixed
     */
    public function resolveField(FieldValue $fieldValue): callable
    {
        return function ($root, array $args) use ($fieldValue) {
            $format = Arr::string($args, 'format');

            /** @var Carbon|null $field */
            $field = $root->getAttribute($this->directiveArgValue('attribute') ?? $fieldValue->getFieldName());

            return $field?->format($format);
        };
    }
}
