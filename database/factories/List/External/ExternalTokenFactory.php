<?php

declare(strict_types=1);

namespace Database\Factories\List\External;

use App\Models\List\External\ExternalToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * Class ExternalTokenFactory.
 *
 * @method ExternalToken createOne($attributes = [])
 * @method ExternalToken makeOne($attributes = [])
 *
 * @extends Factory<ExternalToken>
 */
class ExternalTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ExternalToken>
     */
    protected $model = ExternalToken::class;

    /**
     * Define the model's default state.
     *
     * @phpstan-ignore-next-line
     * @return array
     */
    public function definition(): array
    {
        return [
            ExternalToken::ATTRIBUTE_ACCESS_TOKEN => Hash::make('accesstoken'),
            ExternalToken::ATTRIBUTE_REFRESH_TOKEN => Hash::make('refreshtoken'),
        ];
    }
}
