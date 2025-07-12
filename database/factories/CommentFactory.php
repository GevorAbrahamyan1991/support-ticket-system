<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'content' => $this->faker->sentence,
        ];
    }
} 