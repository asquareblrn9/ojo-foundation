<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class VolunteerApplicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'location' => fake()->city(),
            'area_of_interest' => fake()->randomElement(['Education', 'Healthcare', 'Outreach', 'Media']),
            'skills' => fake()->sentence(),
            'availability' => fake()->randomElement(['Weekdays', 'Weekends', 'Flexible']),
            'motivation' => fake()->paragraph(),
            'status' => ApplicationStatus::Pending,
        ];
    }
}
