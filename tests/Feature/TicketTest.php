<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_ticket()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);
        $response = $this->post('/tickets', [
            'title' => 'Test Ticket',
            'description' => 'Test description',
            'category' => 'General',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'customer_id' => $customer->id,
        ]);
    }

    public function test_agent_can_assign_themselves_to_ticket()
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $customer = User::factory()->create(['role' => 'customer']);
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id]);
        $this->actingAs($agent);
        $response = $this->post(route('tickets.assign', $ticket));
        $response->assertRedirect();
        $ticket->refresh();
        $this->assertEquals($agent->id, $ticket->agent_id);
    }

    public function test_agent_can_update_ticket_status()
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $customer = User::factory()->create(['role' => 'customer']);
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id, 'agent_id' => $agent->id]);
        $this->actingAs($agent);
        $response = $this->post(route('tickets.updateStatus', $ticket), [
            'status' => 'closed',
        ]);
        $response->assertRedirect();
        $ticket->refresh();
        $this->assertEquals('closed', $ticket->status);
    }

    public function test_customer_can_comment_on_own_ticket()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id]);
        $this->actingAs($customer);
        $response = $this->post(route('tickets.addComment', $ticket), [
            'content' => 'Customer comment',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $customer->id,
            'content' => 'Customer comment',
        ]);
    }

    public function test_agent_can_comment_on_ticket()
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $customer = User::factory()->create(['role' => 'customer']);
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id, 'agent_id' => $agent->id]);
        $this->actingAs($agent);
        $response = $this->post(route('tickets.addComment', $ticket), [
            'content' => 'Agent comment',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $agent->id,
            'content' => 'Agent comment',
        ]);
    }
} 