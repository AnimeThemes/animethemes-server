<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth\User\Me;

use App\Actions\Http\Api\ShowAction;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Auth\User\MySchema;
use App\Http\Api\Schema\Schema;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\Auth\User\Resource\MyResource;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class MyController.
 */
class MyController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  ShowRequest  $request
     * @param  ShowAction  $action
     * @return MyResource
     */
    public function show(ShowRequest $request, ShowAction $action): MyResource
    {
        $query = new Query($request->validated());

        /** @var User $user */
        $user = Auth::user();

        $show = $action->show($user, $query, $request->schema());

        return new MyResource($show, $query);
    }

    /**
     * Get the underlying schema.
     *
     * @return Schema
     */
    public function schema(): Schema
    {
        return new MySchema();
    }
}
