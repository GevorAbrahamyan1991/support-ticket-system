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
        $response = $this->post(route('tickets.assign', $ticket), [
            'agent_id' => $agent->id,
        ]);
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

    public function test_agent_can_assign_ticket_to_any_agent()
    {
        $agent1 = User::factory()->create(['role' => 'agent']);
        $agent2 = User::factory()->create(['role' => 'agent']);
        $customer = User::factory()->create(['role' => 'customer']);
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id]);
        $this->actingAs($agent1);
        $response = $this->post(route('tickets.assign', $ticket), [
            'agent_id' => $agent2->id,
        ]);
        $response->assertRedirect();
        $ticket->refresh();
        $this->assertEquals($agent2->id, $ticket->agent_id);
    }

    public function test_customer_cannot_assign_ticket()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id]);
        $this->actingAs($customer);
        $response = $this->post(route('tickets.assign', $ticket), [
            'agent_id' => $customer->id,
        ]);
        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_update_status()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id]);
        $this->actingAs($customer);
        $response = $this->post(route('tickets.updateStatus', $ticket), [
            'status' => 'closed',
        ]);
        $response->assertStatus(403);
    }

    public function test_ticket_creation_stores_geolocation_metadata()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);
        $response = $this->post('/tickets', [
            'title' => 'Geo Test',
            'description' => 'Geo test description',
            'category' => 'General',
        ]);
        $response->assertRedirect();
        $ticket = Ticket::latest()->first();
        $this->assertArrayHasKey('ip', $ticket->metadata);
        $this->assertArrayHasKey('location', $ticket->metadata);
    }
} 