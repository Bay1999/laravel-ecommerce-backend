<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'name' => fake()->name(),
      'slug' => fake()->slug(),
      'parent_category_id' => null,
      'image' => $this->faker->imageUrl(640, 480, 'business'),
    ];
  }

  public function subCategory($parentId)
  {
    return $this->state(function (array $attributes) use ($parentId) {
      return [
        'parent_category_id' => $parentId,
      ];
    });
  }
}
