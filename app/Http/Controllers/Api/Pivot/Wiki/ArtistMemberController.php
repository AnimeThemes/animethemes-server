<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pivot\Wiki;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\Pivot\PivotController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\Pivot\Wiki\Collection\ArtistMemberCollection;
use App\Http\Resources\Pivot\Wiki\Resource\ArtistMemberJsonResource;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistMember;
use Illuminate\Http\JsonResponse;

class ArtistMemberController extends PivotController
{
    public function __construct()
    {
        parent::__construct(Artist::class, 'artist', Artist::class, 'member');
    }

    public function index(IndexRequest $request, IndexAction $action): ArtistMemberCollection
    {
        $query = new Query($request->validated());

        $resources = $action->index(ArtistMember::query(), $query, $request->schema());

        return new ArtistMemberCollection($resources, $query);
    }

    /**
     * @param  StoreAction<ArtistMember>  $action
     */
    public function store(StoreRequest $request, Artist $artist, Artist $member, StoreAction $action): ArtistMemberJsonResource
    {
        $validated = array_merge(
            $request->validated(),
            [
                ArtistMember::ATTRIBUTE_ARTIST => $artist->getKey(),
                ArtistMember::ATTRIBUTE_MEMBER => $member->getKey(),
            ]
        );

        $artistMember = $action->store(ArtistMember::query(), $validated);

        return new ArtistMemberJsonResource($artistMember, new Query());
    }

    public function show(ShowRequest $request, Artist $artist, Artist $member, ShowAction $action): ArtistMemberJsonResource
    {
        $artistMember = ArtistMember::query()
            ->where(ArtistMember::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistMember::ATTRIBUTE_MEMBER, $member->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $show = $action->show($artistMember, $query, $request->schema());

        return new ArtistMemberJsonResource($show, $query);
    }

    public function update(UpdateRequest $request, Artist $artist, Artist $member, UpdateAction $action): ArtistMemberJsonResource
    {
        $artistMember = ArtistMember::query()
            ->where(ArtistMember::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistMember::ATTRIBUTE_MEMBER, $member->getKey())
            ->firstOrFail();

        $query = new Query($request->validated());

        $updated = $action->update($artistMember, $request->validated());

        return new ArtistMemberJsonResource($updated, $query);
    }

    public function destroy(Artist $artist, Artist $member, DestroyAction $action): JsonResponse
    {
        $artistMember = ArtistMember::query()
            ->where(ArtistMember::ATTRIBUTE_ARTIST, $artist->getKey())
            ->where(ArtistMember::ATTRIBUTE_MEMBER, $member->getKey())
            ->firstOrFail();

        $action->destroy($artistMember);

        return new JsonResponse([
            'message' => "Member '{$member->getName()}' has been detached from Artist '{$artist->getName()}'.",
        ]);
    }
}
