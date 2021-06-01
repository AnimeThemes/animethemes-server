<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Concerns\Http\Controllers\StreamsContent;
use App\Models\Image;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class ImageController.
 */
class ImageController extends Controller
{
    use StreamsContent;

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
