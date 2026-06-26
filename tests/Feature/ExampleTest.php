<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Client;
use App\Models\Project;
use App\Models\Payment;
use App\Models\Act;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the dashboard API returns successful response with data.
     */
    public function test_dashboard_api_returns_data(): void
    {
        // Create test data
        $client = Client::create([
            'name' => 'Test Client',
            'inn' => '1234567890',
            'ogrn' => '1234567890123'
        ]);

        $project = Project::create([
            'name' => 'Test Project',
            'client_id' => $client->id,
            'status' => 'active'
        ]);

        $payment = Payment::create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'payment_date' => now(),
            'amount' => 100000,
            'payment_purpose' => 'Test payment',
            'service_stage' => 'Test stage'
        ]);

        Act::create([
            'payment_id' => $payment->id,
            'is_sent' => false,
            'is_signed' => false
        ]);

        // Test API endpoint
        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'summary' => [
                         'total_amount',
                         'payments_count',
                         'projects_count'
                     ],
                     'projects',
                     'payments',
                     'filters'
                 ]);
    }

    /**
     * Test act status creation.
     */
    public function test_can_create_act(): void
    {
        $client = Client::create([
            'name' => 'Test Client 2',
            'inn' => '0987654321',
            'ogrn' => '0987654321098'
        ]);

        $project = Project::create([
            'name' => 'Test Project 2',
            'client_id' => $client->id,
            'status' => 'active'
        ]);

        $payment = Payment::create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'payment_date' => now(),
            'amount' => 50000,
            'payment_purpose' => 'Another test payment',
            'service_stage' => 'Another stage'
        ]);

        $response = $this->postJson('/api/acts', [
            'payment_id' => $payment->id,
            'is_sent' => true,
            'is_signed' => false
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                     'payment_id' => $payment->id,
                     'is_sent' => true,
                     'is_signed' => false
                 ]);
    }

    /**
     * Test act status update.
     */
    public function test_can_update_act(): void
    {
        $client = Client::create([
            'name' => 'Test Client 3',
            'inn' => '1111111111',
            'ogrn' => '1111111111111'
        ]);

        $project = Project::create([
            'name' => 'Test Project 3',
            'client_id' => $client->id,
            'status' => 'active'
        ]);

        $payment = Payment::create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'payment_date' => now(),
            'amount' => 75000,
            'payment_purpose' => 'Third test payment',
            'service_stage' => 'Third stage'
        ]);

        $act = Act::create([
            'payment_id' => $payment->id,
            'is_sent' => true,
            'is_signed' => false
        ]);

        $response = $this->patchJson("/api/acts/{$act->id}", [
            'is_signed' => true
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'is_signed' => true
                 ]);
    }

    /**
     * Test dashboard filtering by project.
     */
    public function test_dashboard_filter_by_project(): void
    {
        $client = Client::create([
            'name' => 'Test Client Filter',
            'inn' => '2222222222',
            'ogrn' => '2222222222222'
        ]);

        $project1 = Project::create([
            'name' => 'Project 1',
            'client_id' => $client->id,
            'status' => 'active'
        ]);

        $project2 = Project::create([
            'name' => 'Project 2',
            'client_id' => $client->id,
            'status' => 'active'
        ]);

        $payment1 = Payment::create([
            'project_id' => $project1->id,
            'client_id' => $client->id,
            'payment_date' => now(),
            'amount' => 100000,
            'payment_purpose' => 'Payment for Project 1',
            'service_stage' => 'Stage 1'
        ]);

        $payment2 = Payment::create([
            'project_id' => $project2->id,
            'client_id' => $client->id,
            'payment_date' => now(),
            'amount' => 200000,
            'payment_purpose' => 'Payment for Project 2',
            'service_stage' => 'Stage 2'
        ]);

        // Filter by project 1
        $response = $this->getJson('/api/dashboard?project_id=' . $project1->id);

        $response->assertStatus(200)
                 ->assertJsonPath('summary.payments_count', 1)
                 ->assertJsonPath('summary.total_amount', 100000);
    }

    /**
     * Test act status calculation logic.
     */
    public function test_act_status_calculation(): void
    {
        $client = Client::create([
            'name' => 'Test Client Status',
            'inn' => '3333333333',
            'ogrn' => '3333333333333'
        ]);

        $project = Project::create([
            'name' => 'Test Project Status',
            'client_id' => $client->id,
            'status' => 'active'
        ]);

        $payment = Payment::create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'payment_date' => now()->subDays(65),
            'amount' => 100000,
            'payment_purpose' => 'Old payment without act',
            'service_stage' => 'Stage'
        ]);

        // No act - should be "not_sent"
        $this->assertNull($payment->act);

        // Create act that is not sent
        $act = Act::create([
            'payment_id' => $payment->id,
            'is_sent' => false,
            'is_signed' => false
        ]);

        $this->assertEquals('not_sent', $act->status);

        // Update to sent
        $act->update(['is_sent' => true, 'sent_at' => now()]);
        $this->assertEquals('awaiting_signature', $act->fresh()->status);

        // Update to signed
        $act->update(['is_signed' => true, 'signed_at' => now()]);
        $this->assertEquals('closed', $act->fresh()->status);
    }
}
