<?php

namespace Tests\Feature\Http\Api;

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

    /**
     * The Theme Index Endpoint shall display the Theme attributes.
     *
     * @return void
     */
    public function testThemeIndexAttributes()
    {
        $themes = Theme::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.theme.index'));

        $response->assertJson([
            'themes' => $themes->map(function ($theme) {
                return static::getData($theme);
            })->toArray(),
        ]);
    }

    /**
     * The Show Theme Endpoint shall display the Theme attributes.
     *
     * @return void
     */
    public function testShowThemeAttributes()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();

        $response = $this->get(route('api.theme.show', ['theme' => $theme]));

        $response->assertJson(static::getData($theme));
    }

    /**
     * The Show Theme Endpoint shall display the anime relation in an 'anime' attribute.
     *
     * @return void
     */
    public function testShowThemeAnimeAttributes()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();

        $response = $this->get(route('api.theme.show', ['theme' => $theme]));

        $response->assertJson([
            'anime' => AnimeTest::getData($theme->anime),
        ]);
    }

    /**
     * The Show Theme Endpoint shall display the song relation in an 'song' attribute.
     *
     * @return void
     */
    public function testShowThemeSongAttributes()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->for(Song::factory())
            ->create();

        $response = $this->get(route('api.theme.show', ['theme' => $theme]));

        $response->assertJson([
            'song' => SongTest::getData($theme->song),
        ]);
    }

    /**
     * The Show Theme Endpoint shall display the entries relation in an 'entries' attribute.
     *
     * @return void
     */
    public function testShowThemeEntriesAttributes()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->has(Entry::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.theme.show', ['theme' => $theme]));

        $response->assertJson([
            'entries' => $theme->entries->map(function ($entry) {
                return EntryTest::getData($entry);
            })->toArray(),
        ]);
    }

    /**
     * Get attributes for Theme resource.
     *
     * @param Theme $theme
     * @return array
     */
    public static function getData(Theme $theme)
    {
        return [
            'id' => $theme->theme_id,
            'type' => strval(optional($theme->type)->description),
            'sequence' => is_null($theme->sequence) ? '' : $theme->sequence,
            'group' => strval($theme->group),
            'slug' => strval($theme->slug),
            'created_at' => $theme->created_at->toJSON(),
            'updated_at' => $theme->updated_at->toJSON(),
        ];
    }
}
