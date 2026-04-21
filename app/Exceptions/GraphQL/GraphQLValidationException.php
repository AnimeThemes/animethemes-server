<?php

declare(strict_types=1);

namespace App\Exceptions\GraphQL;

use Nuwave\Lighthouse\Exceptions\ValidationException;

class GraphQLValidationException extends ValidationException
{
    /** @return array<string, mixed> */
    public function getExtensions(): array
    {
        return [
            'category' => 'validation',
            static::KEY => $this->validator->errors()->messages(),
        ];
    }
}
