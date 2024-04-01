<?php

declare(strict_types=1);

namespace Database\Factories\List\External;

use App\Models\List\External\ExternalEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class ExternalEntryFactory.
 *
 * @method ExternalEntry createOne($attributes = [])
 * @method ExternalEntry makeOne($attributes = [])
 *
 * @extends Factory<ExternalEntry>
 */
class ExternalEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<ExternalEntry>
     */
    protected $model = ExternalEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
}
