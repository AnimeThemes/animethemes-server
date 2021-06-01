<?php declare(strict_types=1);

namespace App\Http\Resources;

use App\Concerns\JsonApi\PerformsResourceQuery;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     title="Image",
 *     description="Image",
 *     type="object",
 *     @OA\Property(property="id",type="integer",description="Primary Key",example=1018),
 *     @OA\Property(property="path",type="string",description="The path of the Image in storage",example="anime/bakemonogatari.png"),
 *     @OA\Property(property="facet",type="string",enum={"Small Cover","Large Cover"},description="THe component of the page the image is intended for",example="Small Cover"),
 *     @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z"),
 *     @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:37:25.000000Z"),
 *     @OA\Property(property="artists",type="array",@OA\Items()),
 *     @OA\Property(property="anime",type="array",@OA\Items(
 *         @OA\Property(property="id",type="integer",description="Primary Key",example=197),
 *         @OA\Property(property="name",type="string",description="The Primary Title of the Anime",example="Bakemonogatari"),
 *         @OA\Property(property="slug",type="string",description="URL Slug & Model Route Key",example="bakemonogatari"),
 *         @OA\Property(property="year",type="integer",description="The Year in which the Anime Premiered",example=2009),
 *         @OA\Property(property="season",type="string",enum={"Winter","Spring","Summer","Fall"},description="The Season in which the Anime Premiered",example="Summer"),
 *         @OA\Property(property="created_at",type="string",description="The Resource Creation Timestamp",example="2020-08-15T05:30:43.000000Z"),
 *         @OA\Property(property="updated_at",type="string",description="The Resource Last Updated Timestamp",example="2020-08-15T05:37:25.000000Z"),
 *     ))
 * )
 */
class ImageResource extends BaseResource
{
    use PerformsResourceQuery;

    /**
     * The "data" wrapper that should be applied.
     *
     * @var string
     */
    public static $wrap = 'image';

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when($this->isAllowedField('id'), $this->image_id),
            'path' => $this->when($this->isAllowedField('path'), $this->path),
            'size' => $this->when($this->isAllowedField('size'), $this->path),
            'mimetype' => $this->when($this->isAllowedField('mimetype'), $this->path),
            'facet' => $this->when($this->isAllowedField('facet'), strval(optional($this->facet)->description)),
            'created_at' => $this->when($this->isAllowedField('created_at'), $this->created_at),
            'updated_at' => $this->when($this->isAllowedField('updated_at'), $this->updated_at),
            'deleted_at' => $this->when($this->isAllowedField('deleted_at'), $this->deleted_at),
            'link' =>  $this->when($this->isAllowedField('link'), route('image.show', $this)),
            'artists' => ArtistCollection::make($this->whenLoaded('artists'), $this->parser),
            'anime' => AnimeCollection::make($this->whenLoaded('anime'), $this->parser),
        ];
    }

    /**
     * The include paths a client is allowed to request.
     *
     * @return array
     */
    public static function allowedIncludePaths(): array
    {
        return [
            'anime',
            'artists',
        ];
    }
}
