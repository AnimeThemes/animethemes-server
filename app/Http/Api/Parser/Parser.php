<?php

declare(strict_types=1);

namespace App\Http\Api\Parser;

/**
 * Class Parser.
 */
abstract class Parser
{
    /**
     * The parameter to parse.
     *
     * @var string|null
     */
    public static ?string $param = null;

    /**
     * Parse parameters to collection.
     *
     * @param  array  $parameters
     * @return array
     */
    abstract public static function parse(array $parameters): array;
}
