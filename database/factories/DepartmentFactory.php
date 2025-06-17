<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Establishment;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Department::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'establishment_id' => Establishment::factory(),
            'name' => $this->faker->company() . ' Department',
            'description' => $this->faker->paragraph(),
        ];
    }
}
