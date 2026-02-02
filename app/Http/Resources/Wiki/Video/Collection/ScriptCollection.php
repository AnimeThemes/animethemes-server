<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Video\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Video\Resource\ScriptJsonResource;
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
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (VideoScript $script): ScriptJsonResource => new ScriptJsonResource($script, $this->query))->all();
    }
}
