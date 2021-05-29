<?php

namespace App\Http\Controllers;

use App\Concerns\Http\Controllers\StreamsContent;
use App\Models\Image;

class ImageController extends Controller
{
    use StreamsContent;

    /**
     * Stream image.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function show(Image $image)
    {
        return $this->streamContent($image);
    }
}
