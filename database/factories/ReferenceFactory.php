<?php

namespace Database\Factories;

use App\Models\Reference;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Reference::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'main_date' => $this->faker->dateTimeBetween('-10 years', 'now'),
            'document_url' => $this->faker->url(),
        ];
    }
}
