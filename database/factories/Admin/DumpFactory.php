<?php

declare(strict_types=1);

namespace Database\Factories\Admin;

use App\Models\Admin\Dump;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @method Dump createOne($attributes = [])
 * @method Dump makeOne($attributes = [])
 *
 * @extends Factory<Dump>
 */
class DumpFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Dump>
     */
    protected $model = Dump::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            Dump::ATTRIBUTE_PATH => Str::random(),
            Dump::ATTRIBUTE_PUBLIC => true,
        ];
    }

    /**
     * Create a private dump.
     */
    public function private(): static
    {
        return $this->state([
            Dump::ATTRIBUTE_PUBLIC => false,
        ]);
    }
}
