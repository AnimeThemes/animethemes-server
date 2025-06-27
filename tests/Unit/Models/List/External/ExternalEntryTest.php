<?php

declare(strict_types=1);

namespace Tests\Unit\Models\List\External;

use App\Enums\Models\List\ExternalEntryWatchStatus;
use App\Models\List\External\ExternalEntry;
use App\Models\List\ExternalProfile;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

/**
 * Class ExternalEntryTest.
 */
class ExternalEntryTest extends TestCase
{
    /**
     * The watch status attribute of an entry shall be cast to a ExternalEntryWatchStatus enum instance.
     *
     * @return void
     */
    public function testCastsWatchStatusToEnum(): void
    {
        $entry = ExternalEntry::factory()
            ->for(ExternalProfile::factory())
            ->createOne();

        $status = $entry->watch_status;

        static::assertInstanceOf(ExternalEntryWatchStatus::class, $status);
    }

    /**
     * The is favorite attribute of an entry shall be cast to a bool.
     *
     * @return void
     */
    public function testCastsIsFavoriteToBool(): void
    {
        $entry = ExternalEntry::factory()
            ->for(ExternalProfile::factory())
            ->createOne();

        $is_favorite = $entry->is_favorite;

        static::assertIsBool($is_favorite);
    }

    /**
     * External entries shall be nameable.
     *
     * @return void
     */
    public function testNameable(): void
    {
        $entry = ExternalEntry::factory()
            ->for(ExternalProfile::factory())
            ->createOne();

        static::assertIsString($entry->getName());
    }

    /**
     * External entries shall have subtitle.
     *
     * @return void
     */
    public function testHasSubtitle(): void
    {
        $entry = ExternalEntry::factory()
            ->for(ExternalProfile::factory())
            ->for(Anime::factory())
            ->createOne();

        static::assertIsString($entry->getSubtitle());
    }

    /**
     * External Entries shall belong to an External Profile.
     *
     * @return void
     */
    public function testProfile(): void
    {
        $entry = ExternalEntry::factory()
            ->for(ExternalProfile::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $entry->externalprofile());
        static::assertInstanceOf(ExternalProfile::class, $entry->externalprofile()->first());
    }

    /**
     * External Entries shall belong to an Anime.
     *
     * @return void
     */
    public function testAnime(): void
    {
        $entry = ExternalEntry::factory()
            ->for(ExternalProfile::factory())
            ->for(Anime::factory())
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $entry->anime());
        static::assertInstanceOf(Anime::class, $entry->anime()->first());
    }
}
