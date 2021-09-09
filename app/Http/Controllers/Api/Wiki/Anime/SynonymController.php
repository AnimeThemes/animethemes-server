<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Anime;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\Wiki\Anime\Synonym\SynonymIndexRequest;
use App\Http\Requests\Api\Wiki\Anime\Synonym\SynonymShowRequest;
use App\Http\Resources\Wiki\Anime\Collection\SynonymCollection;
use App\Http\Resources\Wiki\Anime\Resource\SynonymResource;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Http\JsonResponse;

/**
 * Class SynonymController.
 */
class SynonymController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SynonymIndexRequest  $request
     * @return JsonResponse
     */
    public function index(SynonymIndexRequest $request): JsonResponse
    {
        if ($this->query->hasSearchCriteria()) {
            return SynonymCollection::performSearch($this->query, PaginationStrategy::OFFSET())->toResponse($request);
        }

        return SynonymCollection::performQuery($this->query)->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  SynonymShowRequest  $request
     * @param  AnimeSynonym  $synonym
     * @return JsonResponse
     */
    public function show(SynonymShowRequest $request, AnimeSynonym $synonym): JsonResponse
    {
        $resource = SynonymResource::performQuery($synonym, $this->query);

        return $resource->toResponse($request);
    }
}
