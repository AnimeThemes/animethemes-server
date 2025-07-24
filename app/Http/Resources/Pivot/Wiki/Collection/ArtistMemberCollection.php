<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Collection;

use App\Http\Resources\BaseCollection;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistMemberResource;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Http\Request;

class ArtistMemberCollection extends BaseCollection
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'artistmembers';

    /**
     * Transform the resource into a JSON array.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(fn (ArtistMember $artistMember) => new ArtistMemberResource($artistMember, $this->query))->all();
    }
}
