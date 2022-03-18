<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Anime;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Wiki\Anime\Synonym\SynonymDestroyRequest;
use App\Http\Requests\Api\Wiki\Anime\Synonym\SynonymForceDeleteRequest;
use App\Http\Requests\Api\Wiki\Anime\Synonym\SynonymIndexRequest;
use App\Http\Requests\Api\Wiki\Anime\Synonym\SynonymRestoreRequest;
use App\Http\Requests\Api\Wiki\Anime\Synonym\SynonymShowRequest;
use App\Http\Requests\Api\Wiki\Anime\Synonym\SynonymStoreRequest;
use App\Http\Requests\Api\Wiki\Anime\Synonym\SynonymUpdateRequest;
use App\Models\Wiki\Anime\AnimeSynonym;
use Illuminate\Http\JsonResponse;

/**
 * Class SynonymController.
 */
class SynonymController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  SynonymIndexRequest  $request
     * @return JsonResponse
     */
    public function index(SynonymIndexRequest $request): JsonResponse
    {
        $query = $request->getQuery();

        if ($query->hasSearchCriteria()) {
            return $query->search(PaginationStrategy::OFFSET())->toResponse($request);
        }

        return $query->index()->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  SynonymStoreRequest  $request
     * @return JsonResponse
     */
    public function store(SynonymStoreRequest $request): JsonResponse
    {
        $anime = AnimeSynonym::query()->create($request->validated());

        $resource = $request->getQuery()->resource($anime);

        return $resource->toResponse($request);
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
        $resource = $request->getQuery()->show($synonym);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  SynonymUpdateRequest  $request
     * @param  AnimeSynonym  $synonym
     * @return JsonResponse
     */
    public function update(SynonymUpdateRequest $request, AnimeSynonym $synonym): JsonResponse
    {
        $synonym->update($request->validated());

        $resource = $request->getQuery()->resource($synonym);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  SynonymDestroyRequest  $request
     * @param  AnimeSynonym  $synonym
     * @return JsonResponse
     */
    public function destroy(SynonymDestroyRequest $request, AnimeSynonym $synonym): JsonResponse
    {
        $synonym->delete();

        $resource = $request->getQuery()->resource($synonym);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  SynonymRestoreRequest  $request
     * @param  AnimeSynonym  $synonym
     * @return JsonResponse
     */
    public function restore(SynonymRestoreRequest $request, AnimeSynonym $synonym): JsonResponse
    {
        $synonym->restore();

        $resource = $request->getQuery()->resource($synonym);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  SynonymForceDeleteRequest  $request
     * @param  AnimeSynonym  $synonym
     * @return JsonResponse
     */
    public function forceDelete(SynonymForceDeleteRequest $request, AnimeSynonym $synonym): JsonResponse
    {
        $synonym->forceDelete();

        $resource = $request->getQuery()->resource(new AnimeSynonym());

        return $resource->toResponse($request);
    }
}
