<?php

declare(strict_types=1);

namespace Database\Factories\Admin;

use App\Models\Admin\Dump;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @method Dump createOne($attributes = [])
 * @method Dump makeOne($attributes = [])
 *
 * @extends Factory<Dump>
 */
#[UseModel(Dump::class)]
class DumpFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Dump::ATTRIBUTE_PATH => Str::random(),
        ];
    }
}
