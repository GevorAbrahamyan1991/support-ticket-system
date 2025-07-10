<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'category' => $this->faker->randomElement(['General', 'Technical', 'Billing']),
            'status' => 'open',
            'customer_id' => User::factory(),
            'agent_id' => null,
            'metadata' => [
                'ip' => $this->faker->ipv4,
                'location' => [
                    'country' => $this->faker->country,
                    'city' => $this->faker->city,
                ],
            ],
        ];
    }
} 