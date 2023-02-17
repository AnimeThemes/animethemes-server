<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth\User\Me;

use App\Http\Api\Query\Query;
use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\Resource\UserResource;
use App\Models\Auth\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class MyController.
 */
class MyController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $resource = new UserResource($user, new Query());

        return $resource->toResponse($request);
    }
}
