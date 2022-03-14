<?php

declare(strict_types=1);

namespace App\Concerns\Http\Requests\Api;

use App\Http\Api\Include\AllowedInclude;
use App\Http\Api\Schema\Schema;

/**
 * Trait ValidatesIncludes.
 */
trait ValidatesIncludes
{
    use ValidatesParameters;

    /**
     * Restrict the allowed values for the schema includes.
     *
     * @param  string  $param
     * @param  Schema  $schema
     * @return array[]
     */
    protected function restrictAllowedIncludeValues(string $param, Schema $schema): array
    {
        return $this->restrictAllowedValues(
            $param,
            collect($schema->allowedIncludes())->map(fn (AllowedInclude $include) => $include->path())
        );
    }
}
