<?php

namespace Database\Factories;

use App\Models\Consultant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsultantFactory extends Factory
{
    protected $model = Consultant::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'dba_company_name' => $this->faker->optional(0.7)->company(),
            'mailing_address' => $this->faker->optional(0.8)->address(),
            'fcp_expiration_date' => $this->faker->optional(0.6)->dateTimeBetween('now', '+2 years'),
            'assigned_light_meter' => $this->faker->optional(0.5)->bothify('LM-###-??'),
            'lm_nist_expiration_date' => $this->faker->optional(0.4)->dateTimeBetween('now', '+1 year'),
            'subcontractor_bonus_rate' => $this->faker->optional(0.3)->randomFloat(2, 5, 25),
            'notes' => $this->faker->optional(0.3)->paragraph(),
        ];
    }

    /**
     * Indicate that the consultant is active and available.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'fcp_expiration_date' => $this->faker->dateTimeBetween('now', '+2 years'),
        ]);
    }

    /**
     * Indicate that the consultant is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'fcp_expiration_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'availability_status' => 'unavailable',
        ]);
    }

    /**
     * Indicate that the consultant is busy.
     */
    public function busy(): static
    {
        return $this->state(fn (array $attributes) => [
            'availability_status' => 'busy',
        ]);
    }

    /**
     * Indicate that the consultant is highly certified.
     */
    public function expert(): static
    {
        return $this->state(fn (array $attributes) => [
            'certification_level' => 'Expert',
            'hourly_rate' => $this->faker->randomFloat(2, 150, 250),
            'specialization' => 'Security Assessment',
        ]);
    }
}
