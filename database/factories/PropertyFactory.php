<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'property_type' => $this->faker->randomElement(['House', 'Flat', 'Apartment']),
            'location' => $this->faker->city(),
            'price' => $this->faker->numberBetween(500000, 5000000),
            'key_features' => $this->faker->words(3, true),
            'tone' => 'Formal',
            'ai_description' => null,
        ];
    }
}
