<?php

namespace Database\Factories;

use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-30 days', 'now');
        $end = $this->faker->dateTimeBetween($start, '+30 days');

        return [
            'period_start' => $start,
            'period_end' => $end,
            'payload' => json_encode([
                'tasks_completed' => $this->faker->numberBetween(5, 50),
                'tasks_pending' => $this->faker->numberBetween(1, 20),
                'total_users' => $this->faker->numberBetween(5, 15),
                'generated_at' => now()->toDateTimeString(),
            ]),
            'path' => $this->faker->filePath(),
        ];
    }
}
