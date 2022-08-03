<?php

namespace Database\Factories;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $categoryuuidArr = $this->getCategoryUuid();
        $catUuid = fake()->randomElement($categoryuuidArr);
        return [
            'category_uuid' => $catUuid,
            'title' => ucfirst(fake()->shuffle('Product-abcdefghijklmnop')),
            'uuid' => fake()->uuid(),
            'price' => fake()->randomFloat(2),
            'description' => fake()->sentence(),
            'metadata' => json_encode(['size' => 'small', 'color' => 'blue', 'shape' => 'circle', 'weight' => '25']),
            'created_at' => Carbon::now()->format('Y-m-d h:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d h:i:s'),
            'deleted_at' => null,
        ];
    }

    public function getCategoryUuid()
    {
        return Category::all()->pluck('uuid')->toArray();
    }
}
