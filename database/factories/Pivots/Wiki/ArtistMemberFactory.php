<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Wiki;

use App\Pivots\Wiki\ArtistMember;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class ArtistMemberFactory.
 *
 * @method ArtistMember createOne($attributes = [])
 * @method ArtistMember makeOne($attributes = [])
 *
 * @extends Factory<ArtistMember>
 */
class ArtistMemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ArtistMember>
     */
    protected $model = ArtistMember::class;

    /**
     * Define the model's default state.
     *
     * @phpstan-ignore-next-line
     * @return array
     */
    public function definition(): array
    {
        return [
            ArtistMember::ATTRIBUTE_AS => Str::random(),
        ];
    }
}
