<?php

namespace Database\Factories;

use App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Grade>
 */
class GradeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Grade::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $grades = [
            ['name' => 'Licence', 'level' => 1, 'description' => 'Bachelor\'s degree equivalent'],
            ['name' => 'Master', 'level' => 2, 'description' => 'Master\'s degree'],
            ['name' => 'Doctorat', 'level' => 3, 'description' => 'Doctoral degree'],
        ];

        $grade = $this->faker->randomElement($grades);

        return [
            'name' => $grade['name'],
            'level' => $grade['level'],
            'description' => $grade['description'],
        ];
    }
}
