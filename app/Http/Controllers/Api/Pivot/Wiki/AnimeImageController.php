<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Pivot\Wiki;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Pivot\Wiki\AnimeImage\AnimeImageDestroyRequest;
use App\Http\Requests\Api\Pivot\Wiki\AnimeImage\AnimeImageIndexRequest;
use App\Http\Requests\Api\Pivot\Wiki\AnimeImage\AnimeImageShowRequest;
use App\Http\Requests\Api\Pivot\Wiki\AnimeImage\AnimeImageStoreRequest;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Image;
use App\Pivots\Wiki\AnimeImage;
use Illuminate\Http\JsonResponse;

/**
 * Class AnimeImageController.
 */
class AnimeImageController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Anime::class, 'anime');
        $this->authorizeResource(Image::class, 'image');
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  AnimeImageIndexRequest  $request
     * @return JsonResponse
     */
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
