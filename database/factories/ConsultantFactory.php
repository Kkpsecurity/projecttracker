<?php

namespace Database\Factories;

use App\Models\Consultant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsultantFactory extends Factory
{
    protected $model = Consultant::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'dba_company_name' => $this->faker->company,
            'mailing_address' => $this->faker->address,
            'fcp_expiration_date' => $this->faker->dateTimeBetween('now', '+2 years'),
            'assigned_light_meter' => $this->faker->randomElement(['LM-001', 'LM-002', 'LM-003']),
            'lm_nist_expiration_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'subcontractor_bonus_rate' => $this->faker->randomFloat(2, 0.05, 0.15),
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}
