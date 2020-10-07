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
     *
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
     *
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
            'anime' => AnimeTest::getData($synonym->anime)
        ]);
    }

    /**
     *
     *
     * @param Synonym $synonym
     * @return array
     */
    public static function getData(Synonym $synonym) {
        return [
            'id' => $synonym->synonym_id,
            'text' => $synonym->text,
            'created_at' => $synonym->created_at->toJSON(),
            'updated_at' => $synonym->updated_at->toJSON()
        ];
    }
}
