<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Api\Criteria\Field;

use App\Http\Api\Criteria\Field\Criteria;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class CriteriaTest.
 */
class CriteriaTest extends TestCase
{
    use WithFaker;

    /**
     * The field criteria shall return true if the field is allowed.
     *
     * @return void
     */
    public function test_is_allowed_field(): void
    {
        $fields = collect($this->faker->words($this->faker->randomDigitNotNull()));

        $criteria = new Criteria($this->faker->word(), $fields);

        static::assertTrue($criteria->isAllowedField($fields->random()));
    }

    /**
     * The field criteria shall return false if the field is not allowed.
     *
     * @return void
     */
    public function test_is_not_allowed(): void
    {
        $fields = collect($this->faker->words($this->faker->randomDigitNotNull()));

        $criteria = new Criteria($this->faker->word(), $fields);

        static::assertFalse($criteria->isAllowedField(Str::random()));
    }
}
