<?php

declare(strict_types=1);

namespace App\Concerns\GraphQL;

use Illuminate\Pipeline\Pipeline;

trait RunMiddlewares
{
    protected function runHttpMiddleware(array $middleware = []): void
    {
        resolve(Pipeline::class)
            ->send(request())
            ->through($middleware)
            ->thenReturn();
    }
}
