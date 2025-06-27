<?php

declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Group;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class GroupTest.
 */
class GroupTest extends TestCase
{
    use WithFaker;

    /**
     * Groups shall be nameable.
     *
     * @return void
     */
    public function test_nameable(): void
    {
        $group = Group::factory()->createOne();

        static::assertIsString($group->getName());
    }

    /**
     * Groups shall have subtitle.
     *
     * @return void
     */
    public function test_has_subtitle(): void
    {
        $group = Group::factory()->createOne();

        static::assertIsString($group->getSubtitle());
    }

    /**
     * Group shall have a one-to-many relationship with the type Theme.
     *
     * @return void
     */
    public function test_themes(): void
    {
        $themeCount = $this->faker->randomDigitNotNull();

        $group = Group::factory()
            ->has(AnimeTheme::factory()->for(Anime::factory())->count($themeCount))
            ->createOne();

        static::assertInstanceOf(HasMany::class, $group->animethemes());
        static::assertEquals($themeCount, $group->animethemes()->count());
        static::assertInstanceOf(AnimeTheme::class, $group->animethemes()->first());
    }
}
