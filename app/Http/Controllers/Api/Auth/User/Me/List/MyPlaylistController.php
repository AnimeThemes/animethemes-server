<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth\User\Me\List;

use App\Actions\Http\Api\IndexAction;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\List\PlaylistSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Resources\List\Collection\PlaylistCollection;
use App\Models\Auth\User;
use App\Models\List\Playlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class MyPlaylistController.
 */
class MyPlaylistController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        parent::__construct(Playlist::class, 'playlist');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return JsonResponse
     */
    public function index(IndexRequest $request, IndexAction $action): JsonResponse
    {
        $query = new Query($request->validated());

        /** @var User $user */
        $user = Auth::user();

        $builder = $user->playlists()->getQuery();

        $playlists = $action->index($builder, $query, $request->schema());

        $collection = new PlaylistCollection($playlists, $query);

        return $collection->toResponse($request);
    }

    /**
     * Get the underlying schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new PlaylistSchema();
    }
}
