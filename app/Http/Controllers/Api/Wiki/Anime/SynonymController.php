<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Wiki\Anime;

use App\Enums\Http\Api\Paging\PaginationStrategy;
use App\Http\Controllers\Api\BaseController;
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
class SynonymController extends BaseController
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct(AnimeSynonym::class, 'animesynonym');
    }

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
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  SynonymShowRequest  $request
     * @param  AnimeSynonym  $animesynonym
     * @return JsonResponse
     */
    public function show(SynonymShowRequest $request, AnimeSynonym $animesynonym): JsonResponse
    {
        $resource = $request->getQuery()->show($animesynonym);

        return $resource->toResponse($request);
    }

    /**
     * Update the specified resource.
     *
     * @param  SynonymUpdateRequest  $request
     * @param  AnimeSynonym  $animesynonym
     * @return JsonResponse
     */
    public function update(SynonymUpdateRequest $request, AnimeSynonym $animesynonym): JsonResponse
    {
        $resource = $request->getQuery()->update($animesynonym);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  SynonymDestroyRequest  $request
     * @param  AnimeSynonym  $animesynonym
     * @return JsonResponse
     */
    public function destroy(SynonymDestroyRequest $request, AnimeSynonym $animesynonym): JsonResponse
    {
        $resource = $request->getQuery()->destroy($animesynonym);

        return $resource->toResponse($request);
    }

    /**
     * Restore the specified resource.
     *
     * @param  SynonymRestoreRequest  $request
     * @param  AnimeSynonym  $animesynonym
     * @return JsonResponse
     */
    public function restore(SynonymRestoreRequest $request, AnimeSynonym $animesynonym): JsonResponse
    {
        $resource = $request->getQuery()->restore($animesynonym);

        return $resource->toResponse($request);
    }

    /**
     * Hard-delete the specified resource.
     *
     * @param  SynonymForceDeleteRequest  $request
     * @param  AnimeSynonym  $animesynonym
     * @return JsonResponse
     */
    public function forceDelete(SynonymForceDeleteRequest $request, AnimeSynonym $animesynonym): JsonResponse
    {
        return $request->getQuery()->forceDelete($animesynonym);
    }
}
