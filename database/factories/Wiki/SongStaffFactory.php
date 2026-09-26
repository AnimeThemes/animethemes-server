<?php

declare(strict_types=1);

namespace Database\Factories\Wiki;

use App\Models\Wiki\Artist;
use App\Models\Wiki\Song;
use App\Models\Wiki\SongStaff;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @method SongStaff createOne($attributes = [])
 * @method SongStaff makeOne($attributes = [])
 *
 * @extends Factory<SongStaff>
 */
#[UseModel(SongStaff::class)]
class SongStaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            SongStaff::ATTRIBUTE_ALIAS => fake()->text(),
            SongStaff::ATTRIBUTE_AS => fake()->text(),
            SongStaff::ATTRIBUTE_ARTIST => Artist::factory(),
            SongStaff::ATTRIBUTE_SONG => Song::factory(),
            SongStaff::ATTRIBUTE_ROLE => fake()->word(),
        ];
    }

    public function potentialMember(): self
    {
        $hasMember = fake()->boolean();

        return $this->state(fn (): array => [
            SongStaff::ATTRIBUTE_MEMBER => $hasMember
                ? Artist::factory()
                : null,
            SongStaff::ATTRIBUTE_MEMBER_ALIAS => $hasMember && fake()->boolean()
                ? fake()->text()
                : null,
            SongStaff::ATTRIBUTE_MEMBER_AS => $hasMember && fake()->boolean()
                ? fake()->text()
                : null,
        ]);
    }
}
