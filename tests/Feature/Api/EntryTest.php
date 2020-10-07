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
     *
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

    public static function getData(Entry $entry) {
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
