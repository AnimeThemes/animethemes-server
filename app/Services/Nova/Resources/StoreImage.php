<?php

declare(strict_types=1);

namespace App\Services\Nova\Resources;

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
     * @param Request $request
     * @param Model $model
     * @param string $attribute
     * @param string $requestAttribute
     * @param string $disk
     * @param string $storagePath
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
            'path' => $file->store($storagePath, $disk),
            'size' => $file->getSize(),
            'mimetype' => $file->getClientMimeType(),
        ];
    }
}
