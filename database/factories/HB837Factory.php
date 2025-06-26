<?php

namespace Database\Factories;

use App\Models\HB837;
use Illuminate\Database\Eloquent\Factories\Factory;

class HB837Factory extends Factory
{
    protected $model = HB837::class;

    public function definition(): array
    {
        return [
            'property_id' => $this->faker->unique()->numberBetween(100000, 999999),
            'folio' => $this->faker->regexify('[A-Z]{2}[0-9]{6}'),
            'owner_name' => $this->faker->name(),
            'property_address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            'zip' => $this->faker->postcode(),
            'county' => $this->faker->county(),
            'assessed_value' => $this->faker->numberBetween(50000, 500000),
            'taxable_value' => $this->faker->numberBetween(40000, 450000),
            'land_value' => $this->faker->numberBetween(10000, 100000),
            'building_value' => $this->faker->numberBetween(30000, 400000),
            'homestead_exemption' => $this->faker->boolean(30),
            'tax_year' => $this->faker->year(),
            'latitude' => $this->faker->latitude(25.0, 30.0), // Florida-ish coordinates
            'longitude' => $this->faker->longitude(-85.0, -80.0),
            'legal_description' => $this->faker->sentence(10),
            'property_type' => $this->faker->randomElement(['Residential', 'Commercial', 'Industrial', 'Agricultural']),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the property has homestead exemption
     */
    public function withHomestead(): static
    {
        return $this->state(fn (array $attributes) => [
            'homestead_exemption' => true,
        ]);
    }

    /**
     * Indicate that the property is commercial
     */
    public function commercial(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_type' => 'Commercial',
            'assessed_value' => $this->faker->numberBetween(200000, 2000000),
            'homestead_exemption' => false,
        ]);
    }

    /**
     * Indicate that the property is residential
     */
    public function residential(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_type' => 'Residential',
            'homestead_exemption' => $this->faker->boolean(60),
        ]);
    }

    /**
     * Create a high-value property
     */
    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'assessed_value' => $this->faker->numberBetween(500000, 2000000),
            'taxable_value' => $this->faker->numberBetween(450000, 1800000),
            'building_value' => $this->faker->numberBetween(400000, 1500000),
        ]);
    }
}
