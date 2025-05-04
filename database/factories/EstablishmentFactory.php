<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Establishment>
 */
class EstablishmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' University',
            'abbreviation' => strtoupper($this->faker->lexify('???')),
            'description' => $this->faker->paragraph(),
            'category_id' => Category::factory(),
            'address' => $this->faker->address(),
            'region' => $this->faker->state(),
            'city' => $this->faker->city(),
            'latitude' => $this->faker->latitude(-25, -12),  // Madagascar's approximate latitude range
            'longitude' => $this->faker->longitude(43, 50),  // Madagascar's approximate longitude range
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'website' => $this->faker->url(),
            'logo_url' => $this->faker->imageUrl(),
            'student_count' => $this->faker->numberBetween(100, 10000),
            'success_rate' => $this->faker->randomFloat(2, 40, 95),
            'professional_insertion_rate' => $this->faker->randomFloat(2, 30, 90),
            'first_habilitation_year' => $this->faker->numberBetween(1960, 2020),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
}
