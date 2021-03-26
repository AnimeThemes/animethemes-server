<?php

namespace App\Http\Controllers;

use App\Concerns\Http\Controllers\StreamsContent;
use App\Models\Image;

class ImageController extends Controller
{
    use StreamsContent;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('without_trashed:image');
    }

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
