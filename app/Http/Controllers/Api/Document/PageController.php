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
use App\Http\Resources\Document\Resource\PageJsonResource;
use App\Models\Document\Page;
use Illuminate\Http\JsonResponse;

class PageController extends BaseController
{
    public function __construct()
    {
        parent::__construct(Page::class, 'page');
    }

    public function index(IndexRequest $request, IndexAction $action): PageCollection
    {
        $query = new Query($request->validated());

        /** @phpstan-ignore-next-line */
        $pages = $action->index(Page::query()->public(), $query, $request->schema());

        return new PageCollection($pages, $query);
    }

    /**
     * @param  StoreAction<Page>  $action
     */
    public function store(StoreRequest $request, StoreAction $action): PageJsonResource
    {
        $page = $action->store(Page::query(), $request->validated());

        return new PageJsonResource($page, new Query());
    }

    public function show(ShowRequest $request, Page $page, ShowAction $action): PageJsonResource
    {
        $query = new Query($request->validated());

        $show = $action->show($page, $query, $request->schema());

        return new PageJsonResource($show, $query);
    }

    public function update(UpdateRequest $request, Page $page, UpdateAction $action): PageJsonResource
    {
        $updated = $action->update($page, $request->validated());

        return new PageJsonResource($updated, new Query());
    }

    public function destroy(Page $page, DestroyAction $action): PageJsonResource
    {
        $deleted = $action->destroy($page);

        return new PageJsonResource($deleted, new Query());
    }

    public function restore(Page $page, RestoreAction $action): PageJsonResource
    {
        $restored = $action->restore($page);

        return new PageJsonResource($restored, new Query());
    }

    public function forceDelete(Page $page, ForceDeleteAction $action): JsonResponse
    {
        $message = $action->forceDelete($page);

        return new JsonResponse([
            'message' => $message,
        ]);
    }
}
