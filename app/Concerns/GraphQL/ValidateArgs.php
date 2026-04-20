<?php

declare(strict_types=1);

namespace App\Concerns\GraphQL;

use Illuminate\Support\Facades\Validator;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Validation\Validator as LighthouseValidator;

trait ValidateArgs
{
    /**
     * @param  class-string<LighthouseValidator>  $validator
     */
    protected function validated(string $validator, ResolveInfo $resolveInfo): array
    {
        $validator = new $validator;

        $validator->setArgs($resolveInfo->argumentSet);

        return Validator::make(
            $resolveInfo->argumentSet->toArray(),
            $validator->rules(),
            $validator->messages(),
            $validator->attributes()
        )->validated();
    }
}
