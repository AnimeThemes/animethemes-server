<?php

declare(strict_types=1);

namespace App\GraphQL\Directives;

use App\Constants\FeatureConstants;
use App\Models\Admin\Feature as FeatureModel;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Schema\Values\FieldValue;
use Nuwave\Lighthouse\Support\Contracts\FieldMiddleware;

class FeatureEnabledDirective extends BaseDirective implements FieldMiddleware
{
    /**
     * Define the directive.
     */
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
        directive @featureEnabled(class: String!) on FIELD_DEFINITION
        GRAPHQL;
    }

    /**
     * Wrap around the final field resolver.
     */
    public function handleField(FieldValue $fieldValue): void
    {
        $feature = $this->directiveArgValue('class');

        if (
            FeatureModel::query()
                ->where(FeatureModel::ATTRIBUTE_NAME, $feature)
                ->where(FeatureModel::ATTRIBUTE_SCOPE, FeatureConstants::NULL_SCOPE)
                ->doesntExist()
        ) {
            return;
        }

        $middleware = new EnsureFeaturesAreActive();

        $middleware->handle(request(), fn () => null, $feature);
    }
}
