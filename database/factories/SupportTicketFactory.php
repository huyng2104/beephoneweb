<?php

namespace Database\Factories;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SupportTicket>
 */
class SupportTicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = SupportTicket::class;

    public function definition(): array
    {
        static $ticketNumber = 1024;

        return [
            'ticket_code' => 'BP-' . ($ticketNumber++),
            'user_id' => User::inRandomOrder()->first()?->id,
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->email(),
            'customer_phone' => $this->faker->phoneNumber(),
            'subject' => $this->faker->sentence(5),
            'description' => $this->faker->paragraph(3),
            'category' => $this->faker->randomElement(['payment', 'warranty', 'shipping', 'return', 'general']),
            'status' => $this->faker->randomElement(['open', 'in_progress', 'resolved', 'closed']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'urgent']),
            'assigned_to' => User::inRandomOrder()->first()?->id,
            'response_count' => $this->faker->numberBetween(0, 5),
            'satisfaction_rating' => $this->faker->randomElement([null, 1, 2, 3, 4, 5]),
            'satisfaction_comment' => $this->faker->optional()->sentence(),
            'first_response_at' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
            'resolved_at' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
            'closed_at' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
