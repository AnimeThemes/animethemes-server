<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Pivot\Wiki\Resource\AnimeStudioResource;
use App\Pivots\Wiki\AnimeStudio;
use Illuminate\Http\Request;

/**
 * Class AnimeStudioCollection.
 */
class AnimeStudioCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'animestudios';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray($request): array
    {
        return $this->collection->map(fn (AnimeStudio $animeStudio) => new AnimeStudioResource($animeStudio, $this->query))->all();
    }
}
