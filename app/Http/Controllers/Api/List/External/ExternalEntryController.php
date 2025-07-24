<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\List\External;

use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\ShowAction;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Middleware\Api\EnabledOnlyOnLocalhost;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Resources\List\External\Collection\ExternalEntryCollection;
use App\Http\Resources\List\External\Resource\ExternalEntryResource;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;

class ExternalEntryController extends BaseController
{
    public function __construct()
    {
        parent::__construct(ExternalEntry::class, 'externalentry,externalprofile');

        $this->middleware(EnabledOnlyOnLocalhost::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexAction  $action
     */
    public function index(IndexRequest $request, ExternalProfile $externalprofile, IndexAction $action): ExternalEntryCollection
    {
        $query = new Query($request->validated());

        $builder = ExternalEntry::query()->where(ExternalEntry::ATTRIBUTE_PROFILE, $externalprofile->getKey());

        $resources = $action->index($builder, $query, $request->schema());

        return new ExternalEntryCollection($resources, $query);
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowAction  $action
     */
    public function show(ShowRequest $request, ExternalProfile $externalprofile, ExternalEntry $externalentry, ShowAction $action): ExternalEntryResource
    {
        $query = new Query($request->validated());

        $show = $action->show($externalentry, $query, $request->schema());

        return new ExternalEntryResource($show, $query);
    }
}
