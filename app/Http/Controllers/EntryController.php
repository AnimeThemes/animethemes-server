<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Theme;
use Illuminate\Http\Request;

class EntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($anime_alias, $theme_slug)
    {
        $entries = Entry::where('theme_id', function($theme_query) use ($anime_alias, $theme_slug) {
            $theme_query->select('theme_id')->from('theme')->where('slug', $theme_slug)->where('anime_id', function ($anime_query) use ($anime_alias) {
                $anime_query->select('anime_id')->from('anime')->where('alias', $anime_alias);
            });
        })->get();

        return view('entry.index', compact('entries', 'anime_alias', 'theme_slug'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($anime_alias, $theme_slug)
    {
        return view('entry.create', compact('anime_alias', 'theme_slug'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($anime_alias, $theme_slug, Request $request)
    {
        $theme = Theme::where('slug', $theme_slug)->where('anime_id', function ($query) use ($anime_alias) {
            $query->select('anime_id')->from('anime')->where('alias', $anime_alias);
        })->firstOrFail();

        $request->validate([
            'version' => ['nullable', 'integer'],
            'episodes' => ['nullable', 'max:192'],
            'nsfw' => ['nullable', 'boolean'],
            'spoiler' => ['nullable', 'boolean'],
            'sfx' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'max:192'],
            'videos' => ['nullable', 'exists:video,video_id'],
        ]);

        $theme->entries()->create($request->all())->videos()->sync($request->input('videos'));

        return redirect()->route('anime.theme.entry.index', [$anime_alias, $theme_slug]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Entry  $entry
     * @return \Illuminate\Http\Response
     */
    public function show($anime_alias, $theme_slug, Entry $entry)
    {
        return view('entry.show')->withEntry($entry);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Entry  $entry
     * @return \Illuminate\Http\Response
     */
    public function edit($anime_alias, $theme_slug, Entry $entry)
    {
        return view('entry.edit', compact('anime_alias', 'theme_slug', 'entry'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Entry  $entry
     * @return \Illuminate\Http\Response
     */
    public function update($anime_alias, $theme_slug, Request $request, Entry $entry)
    {
        $request->validate([
            'version' => ['nullable', 'integer'],
            'episodes' => ['nullable', 'max:192'],
            'nsfw' => ['nullable', 'boolean'],
            'spoiler' => ['nullable', 'boolean'],
            'sfx' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'max:192'],
            'videos' => ['nullable', 'exists:video,video_id'],
        ]);

        $entry->videos()->sync($request->input('videos'));
        $entry->update($request->all());

        return redirect()->route('anime.theme.entry.index', [$anime_alias, $theme_slug]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Entry  $entry
     * @return \Illuminate\Http\Response
     */
    public function destroy($anime_alias, $theme_slug, Entry $entry)
    {
        $entry->delete();

        return redirect()->route('anime.theme.entry.index', [$anime_alias, $theme_slug]);
    }
}
