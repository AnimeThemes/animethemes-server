<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ArtistCollection;
use App\Http\Resources\ArtistResource;
use App\Models\Artist;
use Illuminate\Support\Str;

class ArtistController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/artist/",
     *     operationId="getArtists",
     *     tags={"Artist"},
     *     summary="Get paginated listing of Artists",
     *     description="Returns listing of Artists",
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is songs, songs.themes, songs.themes.anime, members, groups & externalResources.",
     *         example="include=songs,members",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="Sort artist resource collection by fields. Case-insensitive options are artist_id, created_at, updated_at, slug & name.",
     *         example="sort=name,-updated_at",
     *         name="sort",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The number of resources to return per page. Acceptable range is [1-30]. Default value is 30.",
     *         example="page[size]=25",
     *         name="page[size]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The page of resources to return.",
     *         example="page[number]=2",
     *         name="page[number]",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[artist]=name,slug",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(@OA\Property(property="artists",type="array", @OA\Items(ref="#/components/schemas/ArtistResource")))
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // initialize builder with eager loaded relations
        $artists = Artist::with($this->parser->getIncludePaths(Artist::$allowedIncludePaths));

        // apply sorts
        foreach ($this->parser->getSorts() as $field => $isAsc) {
            if (in_array(Str::lower($field), Artist::$allowedSortFields)) {
                $artists = $artists->orderBy(Str::lower($field), $isAsc ? 'asc' : 'desc');
            }
        }

        // paginate
        $artists = $artists->jsonPaginate();

        $collection = ArtistCollection::make($artists, $this->parser);

        return $collection->toResponse(request());
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/artist/{slug}",
     *     operationId="getArtist",
     *     tags={"Artist"},
     *     summary="Get properties of Artist",
     *     description="Returns properties of Artist",
     *     @OA\Parameter(
     *         description="Comma-separated list of included related resources. Allowed list is songs, songs.themes, songs.themes.anime, members, groups & externalResources.",
     *         example="include=songs,members",
     *         name="include",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         description="The comma-separated list of fields by resource type",
     *         example="fields[artist]=name,slug",
     *         name="fields",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful",
     *         @OA\JsonContent(ref="#/components/schemas/ArtistResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Resource Not Found!"
     *     )
     * )
     *
     * @param  \App\Models\Artist  $artist
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Artist $artist)
    {
        $resource = ArtistResource::make($artist->load($this->parser->getIncludePaths(Artist::$allowedIncludePaths)), $this->parser);

        return $resource->toResponse(request());
    }
}
