<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Config;

use App\Constants\Config\WikiConstants;
use App\Http\Api\Field\Field;
use App\Http\Api\Parser\FieldParser;
use App\Http\Api\Query\Query;
use App\Http\Api\Schema\Config\WikiSchema;
use App\Http\Resources\Config\Resource\WikiResource;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Class WikiShowTest.
 */
class WikiShowTest extends TestCase
{
    use WithFaker;
    use WithoutEvents;

    /**
     * By default, the Wiki Show Endpoint shall return a Flags Resource.
     *
     * @return void
     */
    public function testDefault(): void
    {
        $pivot = AnimeThemeEntryVideo::factory()
            ->for(Video::factory())
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->createOne();

        Config::set(WikiConstants::FEATURED_ENTRY_SETTING_QUALIFIED, $pivot->entry_id);
        Config::set(WikiConstants::FEATURED_VIDEO_SETTING_QUALIFIED, $pivot->video_id);

        $response = $this->get(route('api.config.wiki.show'));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new WikiResource(new Query()))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }

    /**
     * The Wiki Show Endpoint shall implement sparse fieldsets.
     *
     * @return void
     */
    public function testSparseFieldsets(): void
    {
        $pivot = AnimeThemeEntryVideo::factory()
            ->for(Video::factory())
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->createOne();

        Config::set(WikiConstants::FEATURED_ENTRY_SETTING_QUALIFIED, $pivot->entry_id);
        Config::set(WikiConstants::FEATURED_VIDEO_SETTING_QUALIFIED, $pivot->video_id);

        $schema = new WikiSchema();

        $fields = collect($schema->fields());

        $includedFields = $fields->random($this->faker->numberBetween(1, $fields->count()));

        $parameters = [
            FieldParser::param() => [
                WikiResource::$wrap => $includedFields->map(fn (Field $field) => $field->getKey())->join(','),
            ],
        ];

        $response = $this->get(route('api.config.wiki.show'));

        $response->assertJson(
            json_decode(
                json_encode(
                    (new WikiResource(new Query($parameters)))
                        ->response()
                        ->getData()
                ),
                true
            )
        );
    }
}
