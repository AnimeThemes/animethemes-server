<?php

declare(strict_types=1);

namespace Database\Factories\Pivots;

use App\Pivots\VideoEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class VideoEntryFactory.
 *
 * @method VideoEntry createOne($attributes = [])
 * @method VideoEntry makeOne($attributes = [])
 */
class VideoEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VideoEntry::class;

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
