<?php

declare(strict_types=1);

namespace App\Contracts\GraphQL;

/**
 * Interface HasDirectives.
 */
interface HasDirectives
{
    /**
     * The directives of the type.
     *
     * @return array<string, array>
     */
    public function directives(): array;
}
