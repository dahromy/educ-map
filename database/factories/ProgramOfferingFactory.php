<?php

namespace Database\Factories;

use App\Models\ProgramOffering;
use App\Models\Establishment;
use App\Models\Department;
use App\Models\Domain;
use App\Models\Grade;
use App\Models\Mention;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramOfferingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProgramOffering::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'establishment_id' => Establishment::factory(),
            'department_id' => Department::factory(),
            'domain_id' => Domain::factory(),
            'grade_id' => Grade::factory(),
            'mention_id' => Mention::factory(),
            'tuition_fees_info' => $this->faker->optional()->sentence(),
            'program_duration_info' => $this->faker->optional()->sentence(),
        ];
    }
}
