<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * Class LocalizedEnumDirective.
 */
class LocalizedEnumDirective extends BaseDirective implements FieldMiddleware
{
    /**
     * Define the directive.
     *
     * @return string
     */
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
        """
        Translate the enum value.
        """
        directive @localizedEnum on FIELD_DEFINITION
        GRAPHQL;
    }

    /**
     * Wrap around the final field resolver.
     *
     * @param  FieldValue  $fieldValue
     * @return void
     */
    public function handleField(FieldValue $fieldValue): void
    {
        $fieldValue->wrapResolver(
            fn (callable $resolver) => function ($root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo) use ($resolver) {

            $enum = $resolver($root, $args, $context, $resolveInfo);

            return $enum->localize();
        });
    }
}
