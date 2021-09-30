<?php

declare(strict_types=1);

namespace App\Services\Nova\Resources;

use App\Models\Wiki\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Class StoreImage.
 */
class StoreImage
{
    /**
     * Store the incoming file upload.
     *
     * @param  Request  $request
     * @param  Model  $model
     * @param  string  $attribute
     * @param  string  $requestAttribute
     * @param  string  $disk
     * @param  string  $storagePath
     * @return array
     */
    public function __invoke(
        Request $request,
        Model $model,
        string $attribute,
        string $requestAttribute,
        string $disk,
        string $storagePath
    ): array {
        $file = $request->file($attribute);

        if ($file === null) {
            return [];
        }

        return [
            Image::ATTRIBUTE_PATH => $file->store($storagePath, $disk),
            Image::ATTRIBUTE_SIZE => $file->getSize(),
            Image::ATTRIBUTE_MIMETYPE => $file->getClientMimeType(),
        ];
    }
}
