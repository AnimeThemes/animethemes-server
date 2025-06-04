<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;

/**
 * Class FeatureEnabledDirective.
 */
class FeatureEnabledDirective extends BaseDirective implements FieldMiddleware
{
    /**
     * Define the directive.
     *
     * @return string
     */
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
        directive @featureEnabled(class: String!) on FIELD_DEFINITION
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

        $isExternalProfileManagementAllowed = Str::of(EnsureFeaturesAreActive::class)
            ->append(':')
            ->append($class)
            ->__toString();

        App::make($isExternalProfileManagementAllowed)->handle(request(), fn () => null);
    }
}
