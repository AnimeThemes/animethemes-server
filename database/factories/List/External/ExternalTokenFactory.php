<?php

declare(strict_types=1);

namespace Database\Factories\List\External;

use App\Models\List\External\ExternalToken;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @method ExternalToken createOne($attributes = [])
 * @method ExternalToken makeOne($attributes = [])
 *
 * @extends Factory<ExternalToken>
 */
#[UseModel(ExternalToken::class)]
class ExternalTokenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            ExternalToken::ATTRIBUTE_ACCESS_TOKEN => Hash::make('accesstoken'),
            ExternalToken::ATTRIBUTE_REFRESH_TOKEN => Hash::make('refreshtoken'),
        ];
    }
}
