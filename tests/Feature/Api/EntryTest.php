<?php

namespace Tests\Feature\Api;

use App\Models\Anime;
use App\Models\Entry;
use App\Models\Theme;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EntryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The Entry Index Endpoint shall display the Entry attributes
     *
     * @return void
     */
    public function testEntryIndexAttributes()
    {
        $entries = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.entry.index'));

        $response->assertJson([
            'entries' => $entries->map(function($entry) {
                return static::getData($entry);
            })->toArray()
        ]);
    }

    /**
     * The Show Entry Endpoint shall display the Entry attributes
     *
     * @return void
     */
    public function testShowEntryAttributes()
    {
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        $response = $this->get(route('api.entry.show', ['entry' => $entry]));

        $response->assertJson(static::getData($entry));
    }

    /**
     * The Show Entry Endpoint shall display the anime relation in an 'anime' attribute
     *
     * @return void
     */
    public function testShowEntryAnimeAttributes()
    {
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        $response = $this->get(route('api.entry.show', ['entry' => $entry]));

        $response->assertJson([
            'anime' => AnimeTest::getData($entry->anime)
        ]);
    }

    /**
     * The Show Entry Endpoint shall display the theme relation in a 'theme' attribute
     *
     * @return void
     */
    public function testShowEntryThemeAttributes()
    {
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->create();

        $response = $this->get(route('api.entry.show', ['entry' => $entry]));

        $response->assertJson([
            'theme' => ThemeTest::getData($entry->theme)
        ]);
    }

    /**
     * The Show Entry Endpoint shall display the videos relation in a 'videos' attribute
     *
     * @return void
     */
    public function testShowEntryVideosAttribute()
    {
        $entry = Entry::factory()
            ->for(Theme::factory()->for(Anime::factory()))
            ->has(Video::factory()->count($this->faker->randomDigitNotNull))
            ->create();

        $response = $this->get(route('api.entry.show', ['entry' => $entry]));

        $response->assertJson([
            'videos' => $entry->videos->map(function($video) {
                return VideoTest::getData($video);
            })->toArray()
        ]);
    }

    /**
     * Get attributes for Entry resource
     *
     * @param Entry $entry
     * @return array
     */
    public static function getData(Entry $entry)
    {
        return [
            'id' => $entry->entry_id,
            'version' => is_null($entry->version) ? '' : $entry->version,
            'episodes' => strval($entry->episodes),
            'nsfw' => $entry->nsfw,
            'spoiler' => $entry->spoiler,
            'notes' => strval($entry->notes),
            'created_at' => $entry->created_at->toJSON(),
            'updated_at' => $entry->updated_at->toJSON()
        ];
    }
}
