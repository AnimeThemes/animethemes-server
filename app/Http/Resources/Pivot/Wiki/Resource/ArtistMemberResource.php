<?php

declare(strict_types=1);

namespace App\Http\Resources\Pivot\Wiki\Resource;

use App\Http\Api\Schema\Pivot\Wiki\ArtistMemberSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Resources\BaseResource;
use App\Http\Resources\Wiki\Resource\ArtistResource;
use App\Pivots\Wiki\ArtistMember as ArtistMemberPivot;
use Illuminate\Http\Request;

/**
 * Class ArtistMemberResource.
 */
class ArtistMemberResource extends BaseResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'artistmember';

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $result = parent::toArray($request);

        $result[ArtistMemberPivot::RELATION_ARTIST] = new ArtistResource($this->whenLoaded(ArtistMemberPivot::RELATION_ARTIST), $this->query);
        $result[ArtistMemberPivot::RELATION_MEMBER] = new ArtistResource($this->whenLoaded(ArtistMemberPivot::RELATION_MEMBER), $this->query);

        return $result;
    }

    /**
     * Get the resource schema.
     *
     * @return Schema
     */
    protected function schema(): Schema
    {
        return new ArtistMemberSchema();
    }
}
