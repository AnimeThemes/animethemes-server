<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Api\Admin\FeaturedTheme;

use App\Enums\Auth\CrudPermission;
use App\Enums\Http\Api\Filter\AllowedDateFormat;
use App\Models\Admin\FeaturedTheme;
use App\Models\Auth\User;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\Wiki\AnimeThemeEntryVideo;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Class FeaturedThemeUpdateTest.
 */
class FeaturedThemeUpdateTest extends TestCase
{
    use WithFaker;

    /**
     * The Featured Theme Update Endpoint shall be protected by sanctum.
     *
     * @return void
     */
    public function testProtected(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        $parameters = FeaturedTheme::factory()->raw();

        $response = $this->put(route('api.featuredtheme.update', ['featuredtheme' => $featuredTheme] + $parameters));

        $response->assertUnauthorized();
    }

    /**
     * The Featured Theme Update Endpoint shall forbid users without the update featured theme permission.
     *
     * @return void
     */
    public function testForbidden(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        $parameters = FeaturedTheme::factory()->raw();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.featuredtheme.update', ['featuredtheme' => $featuredTheme] + $parameters));

        $response->assertForbidden();
    }

    /**
     * The Featured Update Store Endpoint shall require the start_at field to be before the end_at field and vice versa.
     *
     * @return void
     */
    public function testStartAtBeforeEndDate(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        $parameters = FeaturedTheme::factory()->raw([
            FeaturedTheme::ATTRIBUTE_START_AT => $this->faker->dateTimeBetween('+1 day', '+1 year')->format(AllowedDateFormat::YMDHISU->value),
            FeaturedTheme::ATTRIBUTE_END_AT => $this->faker->dateTimeBetween('-1 year', '-1 day')->format(AllowedDateFormat::YMDHISU->value),
        ]);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(FeaturedTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.featuredtheme.update', ['featuredtheme' => $featuredTheme] + $parameters));

        $response->assertJsonValidationErrors([
            FeaturedTheme::ATTRIBUTE_START_AT,
            FeaturedTheme::ATTRIBUTE_END_AT,
        ]);
    }

    /**
     * The Featured Theme Update Endpoint shall require the entry and video to have an association.
     *
     * @return void
     */
    public function testAnimeThemeEntryVideoExists(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->create();

        $video = Video::factory()->create();

        $parameters = FeaturedTheme::factory()->raw([
            FeaturedTheme::ATTRIBUTE_ENTRY => $entry->getKey(),
            FeaturedTheme::ATTRIBUTE_VIDEO => $video->getKey(),
        ]);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(FeaturedTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.featuredtheme.update', ['featuredtheme' => $featuredTheme] + $parameters));

        $response->assertJsonValidationErrors([
            FeaturedTheme::ATTRIBUTE_ENTRY,
            FeaturedTheme::ATTRIBUTE_VIDEO,
        ]);
    }

    /**
     * The Featured Theme Update Endpoint shall update a featured theme.
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $featuredTheme = FeaturedTheme::factory()->createOne();

        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $parameters = FeaturedTheme::factory()->raw([
            FeaturedTheme::ATTRIBUTE_ENTRY => $entryVideo->entry_id,
            FeaturedTheme::ATTRIBUTE_VIDEO => $entryVideo->video_id,
        ]);

        $user = User::factory()->withPermissions(CrudPermission::UPDATE->format(FeaturedTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->put(route('api.featuredtheme.update', ['featuredtheme' => $featuredTheme] + $parameters));

        $response->assertOk();
    }
}
