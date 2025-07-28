<?php

declare(strict_types=1);

namespace App\GraphQL\Support\Directives\Filters;

use App\Enums\Http\Api\Filter\ComparisonOperator;
use App\GraphQL\Support\Argument;

final readonly class LikeFilterDirective extends FilterDirective
{
    /**
     * The argument for the filter directive.
     */
    public function argument(): Argument
    {
        return new Argument($this->field->getName().'_like', $this->type)
            ->directives([
                'where' => [
                    'operator' => ComparisonOperator::LIKE->value,
                    'key' => $this->field->getColumn(),
                ],
            ]);
    }
}
