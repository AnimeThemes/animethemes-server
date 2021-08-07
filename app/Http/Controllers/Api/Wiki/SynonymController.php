<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Wiki\Collection\SynonymCollection;
use App\Http\Resources\Wiki\Resource\SynonymResource;
use App\Models\Wiki\Synonym;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class SynonymController.
 */
class SynonymController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if ($this->query->hasSearchCriteria()) {
            return SynonymCollection::performSearch($this->query, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return SynonymCollection::performQuery($this->query)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Synonym $synonym
     * @return JsonResponse
     */
    public function show(Request $request, Synonym $synonym): JsonResponse
    {
        $resource = SynonymResource::performQuery($synonym, $this->query);

        return $resource->toResponse($request);
    }
}
