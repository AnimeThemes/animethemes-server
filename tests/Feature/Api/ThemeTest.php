<?php

namespace Tests\Feature\Api;

use App\Models\Anime;
use App\Models\Entry;
use App\Models\Song;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ThemeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testShowThemeAttributes()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();

        $response = $this->get(route('api.theme.show', ['theme' => $theme]));

        $response->assertJson(static::getData($theme));
    }

    public function testShowThemeAnimeAttributes()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();

        $response = $this->get(route('api.theme.show', ['theme' => $theme]));

        $response->assertJson([
            'anime' => AnimeTest::getData($theme->anime)
        ]);
    }

    public function testShowThemeSongAttributes()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->for(Song::factory())
            ->create();

        $response = $this->get(route('api.theme.show', ['theme' => $theme]));

        $response->assertJson([
            'song' => SongTest::getData($theme->song)
        ]);
    }

    public function testShowThemeEntriesAttributes()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->has(Entry::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.theme.show', ['theme' => $theme]));

        $response->assertJson([
            'entries' => $theme->entries->map(function($entry) {
                return EntryTest::getData($entry);
            })->toArray()
        ]);
    }

    public static function getData(Theme $theme) {
        return [
            'id' => $theme->theme_id,
            'type' => strval(optional($theme->type)->description),
            'sequence' => is_null($theme->sequence) ? '' : $theme->sequence,
            'group' => strval($theme->group),
            'slug' => strval($theme->slug),
            'created_at' => $theme->created_at->toJSON(),
            'updated_at' => $theme->updated_at->toJSON()
        ];
    }
}
