<?php

namespace Database\Factories;

use App\Models\OfficialDocument;
use App\Models\Reference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OfficialDocument>
 */
class OfficialDocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OfficialDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentTypes = ['decree', 'regulation', 'law', 'circular', 'guide', 'form'];

        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'document_url' => $this->faker->url(),
            'document_path' => null,
            'document_type' => $this->faker->randomElement($documentTypes),
            'reference_id' => null, // Will be set when needed
            'file_size' => $this->faker->numberBetween(100000, 5000000), // 100KB to 5MB
            'mime_type' => $this->faker->randomElement(['application/pdf', 'application/msword', 'text/plain']),
            'sort_order' => $this->faker->numberBetween(0, 100),
            'is_active' => $this->faker->boolean(95), // 95% chance of being active
        ];
    }

    /**
     * Indicate that the document should be active.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the document should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the document should be linked to a reference.
     */
    public function withReference(): static
    {
        return $this->state(fn(array $attributes) => [
            'reference_id' => Reference::factory(),
        ]);
    }
}
