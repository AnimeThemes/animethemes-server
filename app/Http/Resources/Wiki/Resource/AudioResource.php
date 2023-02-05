<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Resource;

use App\Http\Api\Query\ReadQuery;
use App\Http\Api\Schema\Schema;
use App\Http\Api\Schema\Wiki\AudioSchema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Collection\VideoCollection;
use App\Models\Wiki\Audio;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\MissingValue;

/**
 * Class AudioResource.
 */
class AudioResource extends BaseResource
{
    final public const ATTRIBUTE_LINK = 'link';

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'audio';

    /**
     * Create a new resource instance.
     *
     * @param  Audio | MissingValue | null  $audio
     * @param  ReadQuery  $query
     * @return void
     */
    public function __construct(Audio|MissingValue|null $audio, ReadQuery $query)
    {
        parent::__construct($audio, $query);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[Audio::RELATION_VIDEOS] = new VideoCollection($this->whenLoaded(Audio::RELATION_VIDEOS), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new AudioSchema();
    }
}
