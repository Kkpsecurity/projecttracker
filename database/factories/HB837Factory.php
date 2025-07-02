<?php

namespace Database\Factories;

use App\Models\HB837;
use App\Models\User;
use App\Models\Consultant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class HB837Factory extends Factory
{
    protected $model = HB837::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'assigned_consultant_id' => null,
            'owner_id' => null,
            'owner_name' => $this->faker->company,
            'property_name' => $this->faker->words(3, true) . ' Property',
            'property_type' => $this->faker->randomElement(['garden', 'midrise', 'highrise', 'industrial', 'bungalo']),
            'units' => $this->faker->numberBetween(10, 500),
            'management_company' => $this->faker->company,
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'county' => $this->faker->city . ' County',
            'state' => $this->faker->stateAbbr,
            'zip' => $this->faker->numerify('#####'),
            'phone' => $this->faker->numerify('###-###-####'),
            'report_status' => $this->faker->randomElement(['not-started', 'in-progress', 'in-review', 'completed']),
            'contracting_status' => $this->faker->randomElement(['quoted', 'started', 'executed', 'closed']),
            'scheduled_date_of_inspection' => $this->faker->optional(0.7)->dateTimeBetween('now', '+2 months'),
            'report_submitted' => $this->faker->optional(0.3)->dateTimeBetween('-1 month', 'now'),
            'billing_req_sent' => $this->faker->optional(0.2)->dateTimeBetween('-1 month', 'now'),
            'agreement_submitted' => $this->faker->optional(0.4)->dateTimeBetween('-2 months', 'now'),
            'quoted_price' => $this->faker->optional(0.8)->randomFloat(2, 1000, 25000),
            'sub_fees_estimated_expenses' => $this->faker->optional(0.5)->randomFloat(2, 100, 5000),
            'project_net_profit' => $this->faker->optional(0.6)->randomFloat(2, 500, 15000),
            'notes' => $this->faker->optional(0.6)->paragraph,
            'financial_notes' => $this->faker->optional(0.3)->sentence,
            'consultant_notes' => $this->faker->optional(0.4)->sentence,
            'securitygauge_crime_risk' => $this->faker->optional(0.5)->randomElement(['Low', 'Medium', 'High']),
            'property_manager_name' => $this->faker->optional(0.7)->name,
            'property_manager_email' => $this->faker->optional(0.7)->email,
            'regional_manager_name' => $this->faker->optional(0.5)->name,
            'regional_manager_email' => $this->faker->optional(0.5)->email,
            'macro_client' => $this->faker->optional(0.4)->company,
            'macro_contact' => $this->faker->optional(0.4)->name,
            'macro_email' => $this->faker->optional(0.4)->email,
            'assigned_consultant' => null, // Legacy field, use relationship instead
        ];
    }

    /**
     * Indicate that the project has a scheduled inspection.
     */
    public function withScheduledInspection(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_date_of_inspection' => $this->faker->dateTimeBetween('now', '+2 months'),
        ]);
    }

    /**
     * Indicate that the project is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'report_status' => 'completed',
            'contracting_status' => 'closed',
            'report_submitted' => $this->faker->dateTimeBetween('-2 months', 'now'),
        ]);
    }

    /**
     * Indicate that the project is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'report_status' => 'in-progress',
            'contracting_status' => 'started',
            'scheduled_date_of_inspection' => $this->faker->dateTimeBetween('-1 week', '+1 month'),
        ]);
    }

    /**
     * Indicate that the project has not started.
     */
    public function notStarted(): static
    {
        return $this->state(fn (array $attributes) => [
            'report_status' => 'not-started',
            'contracting_status' => 'quoted',
            'scheduled_date_of_inspection' => $this->faker->optional(0.5)->dateTimeBetween('now', '+2 months'),
        ]);
    }

    /**
     * Indicate that the project has an assigned consultant.
     */
    public function withConsultant(): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_consultant_id' => Consultant::factory(),
        ]);
    }
}
