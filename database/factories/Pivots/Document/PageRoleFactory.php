<?php

declare(strict_types=1);

namespace Database\Factories\Pivots\Document;

use App\Enums\Pivots\Document\PageRoleType;
use App\Pivots\Document\PageRole;
use Illuminate\Database\Eloquent\Factories\Attributes\UseModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @method PageRole createOne($attributes = [])
 * @method PageRole makeOne($attributes = [])
 *
 * @extends Factory<PageRole>
 */
#[UseModel(PageRole::class)]
class PageRoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = Arr::random(PageRoleType::cases());

        return [
            PageRole::ATTRIBUTE_TYPE => $type->value,
        ];
    }
}
