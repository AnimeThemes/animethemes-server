<?php

declare(strict_types=1);

namespace Database\Factories\Pivots;

use App\Pivots\ArtistMember;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Class ArtistMemberFactory.
 */
class ArtistMemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ArtistMember::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'as' => Str::random(),
        ];
    }
}
