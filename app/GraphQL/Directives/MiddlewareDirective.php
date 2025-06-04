<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use Illuminate\Support\Facades\App;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Schema\Values\TypeValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;
use Nuwave\Lighthouse\Support\Contracts\TypeMiddleware;

/**
 * Class MiddlewareDirective.
 */
class MiddlewareDirective extends BaseDirective implements FieldMiddleware, TypeMiddleware
{
    /**
     * Define the directive.
     *
     * @return string
     */
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
        directive @middleware(class: String!) on OBJECT | ARGUMENT_DEFINITION
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
        $class = $this->directiveArgValue('class');

        App::make($class)->handle(request(), fn () => null);
    }

    /**
     * Handle a type AST as it is converted to an executable type.
     *
     * @param  TypeValue  $value
     * @return void
     */
    public function handleNode(TypeValue $value): void
    {
        $class = $this->directiveArgValue('class');

        App::make($class)->handle(request(), fn () => null);
    }
}
