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

/**
 * Class MyExternalProfileController.
 */
class MyExternalProfileController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(Authenticate::using('sanctum'));
        parent::__construct(ExternalProfile::class, 'externalprofile');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexRequest  $request
     * @param  IndexAction  $action
     * @return ExternalProfileCollection
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
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function schema(): Schema
    {
        return new ExternalProfileSchema();
    }
}
