<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Document;

use App\Actions\Http\Api\DestroyAction;
use App\Actions\Http\Api\ForceDeleteAction;
use App\Actions\Http\Api\IndexAction;
use App\Actions\Http\Api\RestoreAction;
use App\Actions\Http\Api\ShowAction;
use App\Actions\Http\Api\StoreAction;
use App\Actions\Http\Api\UpdateAction;
use App\Http\Api\Query\Query;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\IndexRequest;
use App\Http\Requests\Api\ShowRequest;
use App\Http\Requests\Api\StoreRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\Document\Collection\PageCollection;
use App\Http\Resources\Document\Resource\PageResource;
use App\Models\Document\Page;
use Illuminate\Http\JsonResponse;

class PageController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Page::class, 'page');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  IndexAction  $action
     */
    public function index(IndexRequest $request, IndexAction $action): PageCollection
    {
        $query = new Query($request->validated());

        $pages = $action->index(Page::query(), $query, $request->schema());

        return new PageCollection($pages, $query);
    }

    /**
     * Store a newly created resource.
     *
     * @param  StoreAction<Page>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): PageResource
    {
        $page = $action->store(Page::query(), $request->validated());

        return new PageResource($page, new Query());
    }

    /**
     * Display the specified resource.
     *
     * @param  ShowAction  $action
     */
    public function show(ShowRequest $request, Page $page, ShowAction $action): PageResource
    {
        $query = new Query($request->validated());

        $show = $action->show($page, $query, $request->schema());

        return new PageResource($show, $query);
    }

    /**
     * Update the specified resource.
     *
     * @param  UpdateAction  $action
     */
    public function update(UpdateRequest $request, Page $page, UpdateAction $action): PageResource
    {
        $updated = $action->update($page, $request->validated());

        return new PageResource($updated, new Query());
    }

    /**
     * Remove the specified resource.
     *
     * @param  DestroyAction  $action
     */
    public function destroy(Page $page, DestroyAction $action): PageResource
    {
        $deleted = $action->destroy($page);

        return new PageResource($deleted, new Query());
    }

    /**
     * Restore the specified resource.
     *
     * @param  RestoreAction  $action
     */
    public function restore(Page $page, RestoreAction $action): PageResource
    {
        $restored = $action->restore($page);

        return new PageResource($restored, new Query());
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  ForceDeleteAction  $action
     */
    public function forceDelete(Page $page, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($page);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
