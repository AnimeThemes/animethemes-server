<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Api\QueryParser;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class BaseResource.
 */
abstract class BaseResource extends JsonResource
{
    /**
     * Sparse field set specified by the client.
     *
     * @var QueryParser|int
     */
    protected QueryParser | int $parser;

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     * @param QueryParser|int $parser
     * @return void
     */
    public function __construct(mixed $resource, QueryParser | int $parser)
    {
        parent::__construct($resource);

        $this->parser = $parser;
    }

    /**
     * Set the parser.
     *
     * @param QueryParser $parser
     * @return static
     */
    public function parser(QueryParser $parser): static
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * Determine if field should be included in the response for this resource.
     *
     * @param string $field
     * @return bool
     */
    protected function isAllowedField(string $field): bool
    {
        return $this->parser->isAllowedField(static::$wrap, $field);
    }
}
