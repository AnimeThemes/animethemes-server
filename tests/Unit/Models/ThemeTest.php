<?php

namespace Tests\Unit\Models;

use App\Models\Anime;
use App\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ThemeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The Theme shall have a generated slug on creation.
     *
     * @return void
     */
    public function testThemeCreatesSlug()
    {
        $theme = Theme::factory()
            ->for(Anime::factory())
            ->create();

        $this->assertArrayHasKey('slug', $theme);
    }
}
