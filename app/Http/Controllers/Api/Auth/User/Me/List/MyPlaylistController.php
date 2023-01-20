<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth\User\Me\List;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Auth\User\Me\List\Playlist\MyPlaylistIndexRequest;
use App\Models\List\Playlist;
use Illuminate\Http\JsonResponse;

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
     * @param  MyPlaylistIndexRequest  $request
     * @return JsonResponse
     */
    public function index(MyPlaylistIndexRequest $request): JsonResponse
    {
        $query = $request->getQuery();

        return $query->index()->toResponse($request);
    }
}
