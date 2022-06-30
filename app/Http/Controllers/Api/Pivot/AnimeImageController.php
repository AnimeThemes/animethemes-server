<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pivot;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Pivot\AnimeImage\AnimeImageDestroyRequest;
use App\Http\Requests\Api\Pivot\AnimeImage\AnimeImageIndexRequest;
use App\Http\Requests\Api\Pivot\AnimeImage\AnimeImageShowRequest;
use App\Http\Requests\Api\Pivot\AnimeImage\AnimeImageStoreRequest;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\AnimeImage;
use Illuminate\Http\JsonResponse;
use Spatie\RouteDiscovery\Attributes\Route;

/**
 * Class AnimeImageController.
 */
class AnimeImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  AnimeImageIndexRequest  $request
     * @return JsonResponse
     */
    #[Route(fullUri: 'animeimage', name: 'animeimage.index')]
    public function index(AnimeImageIndexRequest $request): JsonResponse
    {
        $query = $request->getQuery();

        return $query->index()->toResponse($request);
    }

    /**
     * Store a newly created resource.
     *
     * @param  AnimeImageStoreRequest  $request
     * @return JsonResponse
     */
    #[Route(fullUri: 'animeimage', name: 'animeimage.store', middleware: 'auth:sanctum')]
    public function store(AnimeImageStoreRequest $request): JsonResponse
    {
        $resource = $request->getQuery()->store();

        return $resource->toResponse($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  AnimeImageShowRequest  $request
     * @param  Anime  $anime
     * @param  Image  $image
     * @return JsonResponse
     */
    #[Route(fullUri: 'animeimage/{anime}/{image}', name: 'animeimage.show')]
    public function show(AnimeImageShowRequest $request, Anime $anime, Image $image): JsonResponse
    {
        $animeImage = AnimeImage::query()
            ->where(AnimeImage::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->firstOrFail();

        $resource = $request->getQuery()->show($animeImage);

        return $resource->toResponse($request);
    }

    /**
     * Remove the specified resource.
     *
     * @param  AnimeImageDestroyRequest  $request
     * @param  Anime  $anime
     * @param  Image  $image
     * @return JsonResponse
     */
    #[Route(fullUri: 'animeimage/{anime}/{image}', name: 'animeimage.destroy', middleware: 'auth:sanctum')]
    public function destroy(AnimeImageDestroyRequest $request, Anime $anime, Image $image): JsonResponse
    {
        $animeImage = AnimeImage::query()
            ->where(AnimeImage::ATTRIBUTE_ANIME, $anime->getKey())
            ->where(AnimeImage::ATTRIBUTE_IMAGE, $image->getKey())
            ->firstOrFail();

        $resource = $request->getQuery()->destroy($animeImage);

        return $resource->toResponse($request);
    }
}
