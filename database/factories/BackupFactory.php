<?php

namespace Database\Factories;

use App\Models\Backup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BackupFactory extends Factory
{
    protected $model = Backup::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'name' => $this->faker->words(3, true).' Backup',
            'tables' => $this->faker->randomElements(['hb837', 'clients', 'consultants', 'users'], $this->faker->numberBetween(1, 3)),
            'user_id' => User::factory(),
            'filename' => $this->faker->slug().'.xlsx',
            'size' => $this->faker->numberBetween(1024, 10485760), // 1KB to 10MB
            'record_count' => $this->faker->numberBetween(50, 1000),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the backup is completed
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the backup is pending
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the backup is failed
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }

    /**
     * Create a large backup
     */
    public function large(): static
    {
        return $this->state(fn (array $attributes) => [
            'size' => $this->faker->numberBetween(10485760, 104857600), // 10MB to 100MB
            'record_count' => $this->faker->numberBetween(1000, 10000),
        ]);
    }
}
