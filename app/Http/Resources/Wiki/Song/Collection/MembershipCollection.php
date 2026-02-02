<?php

declare(strict_types=1);

namespace App\Http\Resources\Wiki\Song\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Wiki\Song\Resource\MembershipJsonResource;
use App\Models\Wiki\Song\Membership;
use Illuminate\Http\Request;

class MembershipCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'memberships';

    /**
     * Transform the resource into a JSON array.
     *
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (Membership $membership): MembershipJsonResource => new MembershipJsonResource($membership, $this->query))->all();
    }
}
