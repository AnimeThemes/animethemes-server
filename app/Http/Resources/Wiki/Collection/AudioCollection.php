<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Resource\AudioResource;
use App\Models\Wiki\Audio;
use Illuminate\Http\Request;

class AudioCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'audios';

    /**
     * Transform the resource into a JSON array.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (Audio $audio) => new AudioResource($audio, $this->query))->all();
    }
}
