<?php

namespace App\Nova;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class StoreImage
{
    /**
     * Store the incoming file upload.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Model $model
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
    ) {
        $file = $request->file($attribute);

        return [
            'path' => $file->store($storagePath, $disk),
            'size' => $file->getSize(),
            'mimetype' => $file->getClientMimeType(),
        ];
    }
}
