<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartnershipEnquiryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organisation' => fake()->company(),
            'contact_name' => fake()->name(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'partnership_type' => fake()->randomElement(['Corporate', 'NGO', 'Government', 'Faith-based']),
            'message' => fake()->paragraph(),
            'status' => ApplicationStatus::Pending,
        ];
    }
}
