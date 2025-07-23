<?php

declare(strict_types=1);

namespace App\Concerns\Http\Requests\Api;

use App\Contracts\Http\Api\Schema\SchemaInterface;
use App\Http\Api\Include\AllowedInclude;
use Illuminate\Support\Arr;

trait ValidatesIncludes
{
    use ValidatesParameters;

    /**
     * Restrict the allowed values for the schema includes.
     * @return array<string, array>
     */
    protected function restrictAllowedIncludeValues(string $param, SchemaInterface $schema): array
    {
        return $this->restrictAllowedValues(
            $param,
            Arr::map($schema->allowedIncludes(), fn (AllowedInclude $include) => $include->path())
        );
    }
}
