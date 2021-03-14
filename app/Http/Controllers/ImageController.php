<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
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
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function show(Image $image)
    {
        return Storage::disk('images')->response($image->path);
    }
}
