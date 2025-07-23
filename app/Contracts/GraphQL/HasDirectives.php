<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL;

interface HasDirectives
{
    /**
     * The directives of the type.
     *
     * @return array<string, array>
     */
    public function directives(): array;
}
