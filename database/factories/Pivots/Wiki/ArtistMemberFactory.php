<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Wiki;

use App\Pivots\Wiki\ArtistMember;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @method ArtistMember createOne($attributes = [])
 * @method ArtistMember makeOne($attributes = [])
 *
 * @extends Factory<ArtistMember>
 */
#[UseModel(ArtistMember::class)]
class ArtistMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            ArtistMember::ATTRIBUTE_ALIAS => Str::random(),
            ArtistMember::ATTRIBUTE_AS => Str::random(),
            ArtistMember::ATTRIBUTE_NOTES => Str::random(),
        ];
    }
}
