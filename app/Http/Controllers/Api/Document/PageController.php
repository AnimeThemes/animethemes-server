<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Document;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Document\Page\PageIndexRequest;
use App\Http\Requests\Api\Document\Page\PageShowRequest;
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
}
