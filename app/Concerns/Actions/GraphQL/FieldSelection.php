<?php

declare(strict_types=1);

namespace App\Concerns\Actions\GraphQL;

use App\GraphQL\ResolveInfo as GraphQLResolveInfo;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;

trait FieldSelection
{
    protected function getSelection(ResolveInfo $resolveInfo, string $fieldName = 'data'): array
    {
        $resolveInfo = new GraphQLResolveInfo($resolveInfo);

        return Arr::get($resolveInfo->getFieldSelectionWithAliases(100), "{$fieldName}.{$fieldName}.selectionSet")
            ?? $resolveInfo->getFieldSelectionWithAliases(100);
    }
}
