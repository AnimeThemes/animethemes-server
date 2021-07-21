<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Concerns\Http\Resources\PerformsConstrainedEagerLoading;
use App\Http\Api\QueryParser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class BaseResource.
 */
abstract class BaseResource extends JsonResource
{
    use PerformsConstrainedEagerLoading;

    /**
     * Sparse field set specified by the client.
     *
     * @var QueryParser
     */
    protected QueryParser $parser;

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     * @param QueryParser $parser
     * @return void
     */
    public function __construct(mixed $resource, QueryParser $parser)
    {
        parent::__construct($resource);

        $this->parser = $parser;
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

    /**
     * The include paths a client is allowed to request.
     *
     * @return string[]
     */
    abstract public static function allowedIncludePaths(): array;

    /**
     * Perform query to prepare model for resource.
     *
     * @param Model $model
     * @param QueryParser $parser
     * @return static
     */
    public static function performQuery(Model $model, QueryParser $parser): static
    {
        return static::make($model->load(static::performConstrainedEagerLoads($parser)), $parser);
    }
}
