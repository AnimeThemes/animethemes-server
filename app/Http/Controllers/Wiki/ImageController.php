<?php

declare(strict_types=1);

namespace App\Http\Controllers\Wiki;

use App\Models\Wiki\Image;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class ImageController.
 */
class ImageController extends StreamableController
{
    /**
     * Stream image.
     *
     * @param Image $image
     * @return StreamedResponse
     */
    public function show(Image $image): StreamedResponse
    {
        return $this->streamContent($image);
    }
}
