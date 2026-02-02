<?php

declare(strict_types=1);

namespace App\Http\Resources\List\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\List\Resource\ExternalProfileJsonResource;
use App\Models\List\ExternalProfile;
use Illuminate\Http\Request;

class ExternalProfileCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'externalprofiles';

    /**
     * Transform the resource into a JSON array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (ExternalProfile $profile): ExternalProfileJsonResource => new ExternalProfileJsonResource($profile, $this->query))->all();
    }
}
