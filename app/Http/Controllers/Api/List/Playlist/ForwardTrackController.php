<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List\Playlist;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\List\Playlist\Forward\ForwardIndexRequest;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Http\JsonResponse;

/**
 * Class ForwardTrackController.
 */
class ForwardTrackController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(PlaylistTrack::class, 'track,playlist');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  ForwardIndexRequest  $request
     * @param  Playlist  $playlist
     * @return JsonResponse
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function index(ForwardIndexRequest $request, Playlist $playlist): JsonResponse
    {
        $query = $request->getQuery();

        return $query->index()->toResponse($request);
    }
}
