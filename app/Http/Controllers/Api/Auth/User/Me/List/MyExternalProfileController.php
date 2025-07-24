<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth\User\Me\List;

use App\Actions\Http\Api\IndexAction;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\List\ExternalProfileSchema;
use App\Http\Api\Schema\Schema;
use App\Http\Controllers\Api\BaseController;
use App\Http\Middleware\Auth\Authenticate;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Resources\List\Collection\ExternalProfileCollection;
use App\Models\Auth\User;
use App\Models\List\ExternalProfile;
use Illuminate\Support\Facades\Auth;

class MyExternalProfileController extends BaseController
{
    public function __construct()
    {
        $this->middleware(Authenticate::using('sanctum'));
        parent::__construct(ExternalProfile::class, 'externalprofile');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexAction  $action
     */
    public function index(IndexRequest $request, IndexAction $action): ExternalProfileCollection
    {
        $query = new Query($request->validated());

        /** @var User $user */
        $user = Auth::user();

        $builder = $user->externalprofiles()->getQuery();

        $profiles = $action->index($builder, $query, $request->schema());

        return new ExternalProfileCollection($profiles, $query);
    }

    /**
     * Get the underlying schema.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function schema(): ExternalProfileSchema
    {
        return new ExternalProfileSchema();
    }
}
