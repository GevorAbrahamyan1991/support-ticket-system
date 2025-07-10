<?php

namespace Tests\Feature;

use App\Jobs\SendTicketNotification;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class JobDispatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_dispatched_on_ticket_creation()
    {
        Queue::fake();
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);
        $this->post('/tickets', [
            'title' => 'Job Test',
            'description' => 'Job test description',
            'category' => 'General',
        ]);
        Queue::assertPushed(SendTicketNotification::class, function ($job) {
            return $job->type === 'created';
        });
    }

    public function test_job_dispatched_on_ticket_reply()
    {
        Queue::fake();
        $customer = User::factory()->create(['role' => 'customer']);
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id]);
        $this->actingAs($customer);
        $this->post(route('tickets.addComment', $ticket), [
            'content' => 'Reply for job dispatch',
        ]);
        Queue::assertPushed(SendTicketNotification::class, function ($job) {
            return $job->type === 'replied';
        });
    }
} 