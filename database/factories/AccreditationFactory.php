<?php

namespace Database\Factories;

use App\Models\Accreditation;
use App\Models\ProgramOffering;
use App\Models\Reference;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccreditationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Accreditation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'program_offering_id' => ProgramOffering::factory(),
            'reference_id' => Reference::factory(),
            'reference_type' => $this->faker->randomElement(['Initial', 'Renewal', 'Extension']),
            'accreditation_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'is_recent' => $this->faker->boolean(30), // 30% chance of being recent
        ];
    }
}
