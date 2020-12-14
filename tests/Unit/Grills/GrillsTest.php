<?php

namespace Tests\Unit\Grills;

use App\Grills\Grill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GrillsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * We shall retrieve a random grill in the grill disk.
     *
     * @return void
     */
    public function testGrillFactoryProducesGrill()
    {
        $fs = Storage::fake('grill');

        $created_grill_count = $this->faker->randomDigitNotNull;
        Collection::times($created_grill_count)->each(function () use ($fs) {
            $file_name = $this->faker->unique()->word();
            $file = File::fake()->image($file_name);
            $fs->put('', $file);
        });

        $grill = Grill::random();

        $this->assertTrue($fs->exists($grill->path));
    }
}
