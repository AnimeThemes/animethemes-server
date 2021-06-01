<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\JsonApi\QueryParser;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class BaseCollection
 * @package App\Http\Resources
 */
abstract class BaseCollection extends ResourceCollection
{
    /**
     * Sparse field set specified by the client.
     *
     * @var QueryParser
     */
    protected QueryParser $parser;

    /**
     * Indicates if all existing request query parameters should be added to pagination links.
     *
     * @var bool
     */
    protected $preserveAllQueryParameters = true;

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     * @param mixed $parser
     * @return void
     */
    public function __construct(mixed $resource, mixed $parser)
    {
        parent::__construct($resource);

        $this->parser = $parser;
    }
}
