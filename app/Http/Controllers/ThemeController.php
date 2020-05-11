<?php

namespace App\Http\Controllers;

use App\Enums\ThemeType;
use App\Models\Anime;
use App\Models\Song;
use App\Models\Theme;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ThemeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($anime_alias)
    {
        $themes = Theme::where('anime_id', function ($query) use ($anime_alias) {
            $query->select('anime_id')->from('anime')->where('alias', $anime_alias);
        })->get();
        return view('theme.index', compact('themes', 'anime_alias'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($anime_alias)
    {
        $songs = Song::all();
        return view('theme.create', compact('anime_alias'))->withThemeTypes(ThemeType::toSelectArray())->withSongs($songs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($anime_alias, Request $request)
    {
        $anime = Anime::where('alias', $anime_alias)->firstOrFail();

        $request->validate([
            'type' => ['required', new EnumValue(ThemeType::class, false)],
            'sequence' => ['nullable', 'integer'],
            'song' => ['nullable', 'exists:song,song_id'],
            'group' => ['nullable', 'max:192'],
        ]);

        $theme = new Theme;
        $theme->fill($request->all());
        $theme->anime()->associate($anime);
        $theme->song()->associate($request->input('song'));
        $theme->save();

        return redirect()->route('anime.theme.index', $anime_alias);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Theme  $theme
     * @return \Illuminate\Http\Response
     */
    public function show($anime_alias, Theme $theme)
    {
        return view('theme.show')->withTheme($theme);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Theme  $theme
     * @return \Illuminate\Http\Response
     */
    public function edit($anime_alias, Theme $theme)
    {
        $songs = Song::all();
        $themeTypes = ThemeType::toSelectArray();
        return view('theme.edit', compact('anime_alias', 'theme', 'songs', 'themeTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Theme  $theme
     * @return \Illuminate\Http\Response
     */
    public function update($anime_alias, Request $request, Theme $theme)
    {
        $anime = Anime::where('alias', $anime_alias)->firstOrFail();

        $request->validate([
            'type' => ['required', new EnumValue(ThemeType::class, false)],
            'sequence' => ['nullable', 'integer'],
            'song' => ['nullable', 'exists:song,song_id'],
            'group' => ['nullable', 'max:192'],
        ]);

        $theme->fill($request->all());
        $theme->song()->associate($request->input('song'));
        $theme->save();

        return redirect()->route('anime.theme.index', $anime_alias);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Theme  $theme
     * @return \Illuminate\Http\Response
     */
    public function destroy($anime_alias, Theme $theme)
    {
        $theme->delete();

        return redirect()->route('anime.theme.index', $anime_alias);
    }
}
