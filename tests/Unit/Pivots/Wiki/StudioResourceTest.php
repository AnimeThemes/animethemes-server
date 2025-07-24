<?php

declare(strict_types=1);

namespace Tests\Unit\Pivots\Wiki;

use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use App\Pivots\Wiki\StudioResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

class StudioResourceTest extends TestCase
{
    /**
     * A StudioResource shall belong to a Studio.
     */
    public function testStudio(): void
    {
        $studioResource = StudioResource::factory()
            ->for(Studio::factory())
            ->for(ExternalResource::factory(), 'resource')
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $studioResource->studio());
        static::assertInstanceOf(Studio::class, $studioResource->studio()->first());
    }

    /**
     * A StudioResource shall belong to an ExternalResource.
     */
    public function testResource(): void
    {
        $studioResource = StudioResource::factory()
            ->for(Studio::factory())
            ->for(ExternalResource::factory(), 'resource')
            ->createOne();

        static::assertInstanceOf(BelongsTo::class, $studioResource->resource());
        static::assertInstanceOf(ExternalResource::class, $studioResource->resource()->first());
    }
}
