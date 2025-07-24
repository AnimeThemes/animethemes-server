<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Video\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Video\Resource\ScriptResource;
use App\Models\Wiki\Video\VideoScript;
use Illuminate\Http\Request;

class ScriptCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'videoscripts';

    /**
     * Transform the resource into a JSON array.
     *
     * @param  Request  $request
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (VideoScript $script) => new ScriptResource($script, $this->query))->all();
    }
}
