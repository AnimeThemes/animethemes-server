<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Document;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Document\Page\PageDestroyRequest;
use App\Http\Requests\Api\Document\Page\PageForceDeleteRequest;
use App\Http\Requests\Api\Document\Page\PageIndexRequest;
use App\Http\Requests\Api\Document\Page\PageRestoreRequest;
use App\Http\Requests\Api\Document\Page\PageShowRequest;
use App\Http\Requests\Api\Document\Page\PageStoreRequest;
use App\Http\Requests\Api\Document\Page\PageUpdateRequest;
use App\Models\Document\Page;
use Illuminate\Http\JsonResponse;

/**
 * Class PageController.
 */
class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  PageIndexRequest  $request
     * @return JsonResponse
     */
    public function index(PageIndexRequest $request): JsonResponse
    {
        $pages = $request->getQuery()->index();

        return $pages->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  PageStoreRequest  $request
     * @return JsonResponse
     */
    public function store(PageStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  PageShowRequest  $request
     * @param  Page  $page
     * @return JsonResponse
     */
    public function show(PageShowRequest $request, Page $page): JsonResponse
    {
        $resource = $request->getQuery()->show($page);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  PageUpdateRequest  $request
     * @param  Page  $page
     * @return JsonResponse
     */
    public function update(PageUpdateRequest $request, Page $page): JsonResponse
    {
        $resource = $request->getQuery()->update($page);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  PageDestroyRequest  $request
     * @param  Page  $page
     * @return JsonResponse
     */
    public function destroy(PageDestroyRequest $request, Page $page): JsonResponse
    {
        $resource = $request->getQuery()->destroy($page);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  PageRestoreRequest  $request
     * @param  Page  $page
     * @return JsonResponse
     */
    public function restore(PageRestoreRequest $request, Page $page): JsonResponse
    {
        $resource = $request->getQuery()->restore($page);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  PageForceDeleteRequest  $request
     * @param  Page  $page
     * @return JsonResponse
     */
    public function forceDelete(PageForceDeleteRequest $request, Page $page): JsonResponse
    {
        return $request->getQuery()->forceDelete($page);
    }
}
