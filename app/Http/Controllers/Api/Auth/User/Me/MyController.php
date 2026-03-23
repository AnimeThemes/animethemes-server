<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth\User\Me;

use App\Actions\Http\Api\ShowAction;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Auth\User\MySchema;
use App\Http\Controllers\Api\BaseController;
use App\Http\Middleware\Auth\Authenticate;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\Auth\User\Resource\MyJsonResource;
use App\Models\Auth\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware(Authenticate::class.':sanctum')]
class MyController extends BaseController
{
    public function show(ShowRequest $request, #[CurrentUser] User $user, ShowAction $action): MyJsonResource
    {
        $query = new Query($request->validated());

        $show = $action->show($user, $query, $request->schema());

        return new MyJsonResource($show, $query);
    }

    /**
     * Get the underlying schema.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function schema(): MySchema
    {
        return new MySchema();
    }
}
