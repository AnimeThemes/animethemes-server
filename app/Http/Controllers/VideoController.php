<?php

namespace App\Http\Controllers;

use App\Enums\SourceType;
use App\Models\Video;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class VideoController extends Controller
{
    public function index()
    {
        return view('video.index');
    }

    public function show(Video $video)
    {
        set_time_limit(0);
        return Storage::disk('spaces')->response($video->path, null, ['Accept-Ranges' => 'bytes']);
    }

    public function edit(Video $video)
    {
        return view('video.edit')->withVideo($video)->withSourceTypes(SourceType::toSelectArray());
    }

    public function update(Request $request, Video $video)
    {
        $request->validate([
            'resolution' => ['nullable', 'integer'], //TODO: custom rule with range
            'nc' => ['nullable', 'boolean'],
            'subbed' => ['nullable', 'boolean'],
            'lyrics' => ['nullable', 'boolean'],
            'uncen' => ['nullable', 'boolean'],
            'trans' => ['nullable', 'boolean'],
            'over' => ['nullable', 'boolean'],
            'source' => ['nullable', new EnumValue(SourceType::class, false)],
        ]);

        $video->resolution = $request->input('resolution');
        $video->nc = $request->has('nc');
        $video->subbed = $request->has('subbed');
        $video->lyrics = $request->has('lyrics');
        $video->uncen = $request->has('uncen');
        $video->trans = $request->has('trans');
        $video->over = $request->has('over');
        $video->source = $request->input('source'); //TODO: fix
        $video->save();

        return redirect()->route('video.index');
    }
}
