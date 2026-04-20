<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use Illuminate\Support\Str;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldResolver;

class LocalizedDirective extends BaseDirective implements FieldResolver
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
directive @localized on FIELD_DEFINITION
GRAPHQL;
    }

    /**
     * Returns a field resolver function.
     *
     * @return callable(mixed, array<string, mixed>, \Nuwave\Lighthouse\Support\Contracts\GraphQLContext, \Nuwave\Lighthouse\Execution\ResolveInfo): mixed
     */
    public function resolveField(FieldValue $fieldValue): callable
    {
        return function ($root) use ($fieldValue) {
            $field = Str::snake(Str::before($fieldValue->getFieldName(), 'Localized'));

            return $root->getAttribute($field)?->localize();
        };
    }
}
