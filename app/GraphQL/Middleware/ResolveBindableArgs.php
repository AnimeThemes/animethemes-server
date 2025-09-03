<?php

declare(strict_types=1);

namespace App\GraphQL\Middleware;

use App\Contracts\GraphQL\Fields\BindableField;
use App\GraphQL\Schema\Fields\Field;
use App\GraphQL\Schema\Types\EloquentType;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Rebing\GraphQL\Support\Middleware;

class ResolveBindableArgs extends Middleware
{
    /**
     * @param  array<string, mixed>  $args
     */
    public function handle($root, array $args, $context, ResolveInfo $resolveInfo, Closure $next)
    {
        $rebingType = $resolveInfo->fieldDefinition->config['rebingType']
            /** @phpstan-ignore-next-line */
            ?? $resolveInfo->fieldDefinition->config['type']->getWrappedType()->config['rebingType'];

        if (! $rebingType instanceof EloquentType) {
            return $next($root, $args, $context, $resolveInfo);
        }

        $bindableFields = collect($rebingType->fieldClasses())
            ->filter(fn (Field $field) => $field instanceof BindableField && Arr::has($args, $field->getName()))
            ->all();

        /** @var Field&BindableField $field */
        foreach ($bindableFields as $field) {
            $resolver = $field->bindResolver($args);

            if ($resolver === null) {
                $args['model'] = $rebingType->model()::query()
                    ->where($field->getColumn(), Arr::get($args, $field->getName()))
                    ->firstOrFail();

                continue;
            }

            $args[$field->getName()] = $resolver;
        }

        return $next($root, $args, $context, $resolveInfo);
    }
}
