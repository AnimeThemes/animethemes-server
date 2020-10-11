<?php

namespace Tests\Feature\Api;

use App\Models\Anime;
use App\Models\Synonym;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SynonymTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The Synonym Index Endpoint shall display the Synonym attributes.
     *
     * @return void
     */
    public function testSynonymIndexAttributes()
    {
        $synonyms = Synonym::factory()
            ->for(Anime::factory())
            ->count($this->faker->randomDigitNotNull)
            ->create();

        $response = $this->get(route('api.synonym.index'));

        $response->assertJson([
            'synonyms' => $synonyms->map(function ($synonym) {
                return static::getData($synonym);
            })->toArray(),
        ]);
    }

    /**
     * The Show Synonym Endpoint shall display the Synonym attributes.
     *
     * @return void
     */
    public function testShowSynonymAttributes()
    {
        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();

        $response = $this->get(route('api.synonym.show', ['synonym' => $synonym]));

        $response->assertJson(static::getData($synonym));
    }

    /**
     * The Show Synonym Endpoint shall display the anime relation in an 'anime' attribute.
     *
     * @return void
     */
    public function testShowSynonymAnimeAttributes()
    {
        $synonym = Synonym::factory()
            ->for(Anime::factory())
            ->create();

        $response = $this->get(route('api.synonym.show', ['synonym' => $synonym]));

        $response->assertJson([
            'anime' => AnimeTest::getData($synonym->anime),
        ]);
    }

    /**
     * Get attributes for Synonym resource.
     *
     * @param Synonym $synonym
     * @return array
     */
    public static function getData(Synonym $synonym)
    {
        return [
            'id' => $synonym->synonym_id,
            'text' => $synonym->text,
            'created_at' => $synonym->created_at->toJSON(),
            'updated_at' => $synonym->updated_at->toJSON(),
        ];
    }
}
