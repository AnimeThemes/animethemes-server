<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots\Wiki;

use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Song;
use App\Pivots\Wiki\SongResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class SongResourceTest extends TestCase
{
    /**
     * An SongResource shall belong to an Song.
     */
    public function testSong(): void
    {
        $songResource = SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), 'resource')
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $songResource->song());
        static::assertInstanceOf(Song::class, $songResource->song()->first());
    }

    /**
     * An SongResource shall belong to an ExternalResource.
     */
    public function testResource(): void
    {
        $songResource = SongResource::factory()
            ->for(Song::factory())
            ->for(ExternalResource::factory(), 'resource')
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $songResource->resource());
        static::assertInstanceOf(ExternalResource::class, $songResource->resource()->first());
    }
}
