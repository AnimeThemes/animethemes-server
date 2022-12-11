<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List\Playlist;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\List\Playlist\Backward\BackwardIndexRequest;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Illuminate\Http\JsonResponse;

/**
 * Class BackwardTrackController.
 */
class BackwardTrackController extends BaseController
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
     * @param  BackwardIndexRequest  $request
     * @param  Playlist  $playlist
     * @return JsonResponse
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function index(BackwardIndexRequest $request, Playlist $playlist): JsonResponse
    {
        $query = $request->getQuery();

        return $query->index()->toResponse($request);
    }
}
