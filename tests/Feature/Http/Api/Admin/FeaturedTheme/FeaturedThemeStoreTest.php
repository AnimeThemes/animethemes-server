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

class FeaturedThemeStoreTest extends TestCase
{
    use WithFaker;

    /**
     * The Featured Theme Store Endpoint shall be protected by sanctum.
     */
    public function testProtected(): void
    {
        $featuredTheme = FeaturedTheme::factory()->makeOne();

        $response = $this->post(route('api.featuredtheme.store', $featuredTheme->toArray()));

        $response->assertUnauthorized();
    }

    /**
     * The Featured Theme Store Endpoint shall forbid users without the create featured theme permission.
     */
    public function testForbidden(): void
    {
        $featuredTheme = FeaturedTheme::factory()->makeOne();

        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.featuredtheme.store', $featuredTheme->toArray()));

        $response->assertForbidden();
    }

    /**
     * The Featured Theme Store Endpoint shall require the end_at and start_at fields.
     */
    public function testRequiredFields(): void
    {
        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(FeaturedTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.featuredtheme.store'));

        $response->assertJsonValidationErrors([
            FeaturedTheme::ATTRIBUTE_END_AT,
            FeaturedTheme::ATTRIBUTE_START_AT,
        ]);
    }

    /**
     * The Featured Theme Store Endpoint shall require the start_at field to be before the end_at field and vice versa.
     */
    public function testStartAtBeforeEndDate(): void
    {
        $parameters = FeaturedTheme::factory()->raw([
            FeaturedTheme::ATTRIBUTE_START_AT => $this->faker->dateTimeBetween('+1 day', '+1 year')->format(AllowedDateFormat::YMDHISU->value),
            FeaturedTheme::ATTRIBUTE_END_AT => $this->faker->dateTimeBetween('-1 year', '-1 day')->format(AllowedDateFormat::YMDHISU->value),
        ]);

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(FeaturedTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.featuredtheme.store', $parameters));

        $response->assertJsonValidationErrors([
            FeaturedTheme::ATTRIBUTE_START_AT,
            FeaturedTheme::ATTRIBUTE_END_AT,
        ]);
    }

    /**
     * The Featured Theme Store Endpoint shall require the entry and video to have an association.
     */
    public function testAnimeThemeEntryVideoExists(): void
    {
        $entry = AnimeThemeEntry::factory()
            ->for(AnimeTheme::factory()->for(Anime::factory()))
            ->create();

        $video = Video::factory()->create();

        $parameters = FeaturedTheme::factory()->raw([
            FeaturedTheme::ATTRIBUTE_ENTRY => $entry->getKey(),
            FeaturedTheme::ATTRIBUTE_VIDEO => $video->getKey(),
        ]);

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(FeaturedTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.featuredtheme.store', $parameters));

        $response->assertJsonValidationErrors([
            FeaturedTheme::ATTRIBUTE_ENTRY,
            FeaturedTheme::ATTRIBUTE_VIDEO,
        ]);
    }

    /**
     * The Featured Theme Store Endpoint shall create a featured theme.
     */
    public function testCreate(): void
    {
        $entryVideo = AnimeThemeEntryVideo::factory()
            ->for(AnimeThemeEntry::factory()->for(AnimeTheme::factory()->for(Anime::factory())))
            ->for(Video::factory())
            ->createOne();

        $parameters = FeaturedTheme::factory()->raw([
            FeaturedTheme::ATTRIBUTE_ENTRY => $entryVideo->entry_id,
            FeaturedTheme::ATTRIBUTE_VIDEO => $entryVideo->video_id,
        ]);

        $user = User::factory()->withPermissions(CrudPermission::CREATE->format(FeaturedTheme::class))->createOne();

        Sanctum::actingAs($user);

        $response = $this->post(route('api.featuredtheme.store', $parameters));

        $response->assertCreated();
        static::assertDatabaseCount(FeaturedTheme::class, 1);
    }
}
