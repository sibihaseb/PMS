<?php

namespace Tests\Feature\Api;

use App\Models\Project;

class BillingStatusTest extends ApiTestCase
{
    public function test_billing_status_returns_org_plan_and_usage(): void
    {
        $user = $this->createOrganizationUser();

        Project::factory()->count(2)->create([
            'organization_id' => $user->organization_id,
        ]);

        $response = $this->actingAsApiUser($user)->getJson('/api/billing/status');

        $response->assertOk()
            ->assertJson([
                'plan' => 'free',
                'projects_used' => 2,
                'projects_limit' => 3,
            ]);
    }
}
