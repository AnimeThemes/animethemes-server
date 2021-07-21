<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Collection\EntryCollection;
use App\Http\Resources\Wiki\Resource\EntryResource;
use App\Models\Wiki\Entry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class EntryController.
 */
class EntryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if ($this->parser->hasSearch()) {
            return EntryCollection::performSearch($this->parser, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return EntryCollection::performQuery($this->parser)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Entry $entry
     * @return JsonResponse
     */
    public function show(Request $request, Entry $entry): JsonResponse
    {
        $resource = EntryResource::performQuery($entry, $this->parser);

        return $resource->toResponse($request);
    }
}
