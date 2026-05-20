<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\LoyaltyMember;
use App\Jobs\ProcessChurnPredictionsJob;
use Tests\TestCase;

class LoyaltyChurnTest extends TestCase
{
    // This trait automatically rolls back database transactions after each test run
    use RefreshDatabase;

    /** @test */
    public function it_can_successfully_seed_and_generate_local_records()
    {
        // 1. Run the database seed process
        $this->seed();

        // 2. Assert that data points were written successfully to your local table
        $this->assertGreaterThan(0, LoyaltyMember::count());

        // 3. Confirm that premium tier records exist to feed your pipeline
        $premiumCount = LoyaltyMember::whereIn('tier', ['Gold', 'Platinum'])->count();
        $this->assertGreaterThan(0, $premiumCount);
    }

    /** @test */
    public function the_dashboard_endpoint_returns_a_successful_response()
    {
        // Seed the system state
        $this->seed();

        // Perform an HTTP request check against your web endpoint
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
    }
}
