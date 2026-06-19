<?php

namespace Tests\Feature\Api\Phase2;

use App\Models\Organization;
use App\Models\User;
use Tests\Feature\Api\ApiTestCase;

class TeamPlanTest extends ApiTestCase
{
    public function test_team_org_can_create_up_to_twenty_projects(): void
    {
        $organization = Organization::factory()->team()->create();
        $user = User::factory()->forOrganization($organization)->owner()->create();

        $this->actingAsApiUser($user);

        for ($i = 1; $i <= 20; $i++) {
            $this->postJson('/api/projects', ['name' => "Project {$i}"])
                ->assertCreated();
        }

        $this->assertDatabaseCount('projects', 20);
    }

    public function test_team_org_gets_402_on_twenty_first_project(): void
    {
        $organization = Organization::factory()->team()->create();
        $user = User::factory()->forOrganization($organization)->owner()->create();

        $this->actingAsApiUser($user);

        for ($i = 1; $i <= 20; $i++) {
            $this->postJson('/api/projects', ['name' => "Project {$i}"])
                ->assertCreated();
        }

        $this->postJson('/api/projects', ['name' => 'Project 21'])
            ->assertStatus(402);
    }

    public function test_billing_status_reflects_team_plan(): void
    {
        $organization = Organization::factory()->team()->create();
        $user = User::factory()->forOrganization($organization)->owner()->create();

        $this->actingAsApiUser($user)
            ->getJson('/api/billing/status')
            ->assertOk()
            ->assertJson([
                'plan' => 'team',
                'projects_used' => 0,
                'projects_limit' => 20,
            ]);
    }
}
