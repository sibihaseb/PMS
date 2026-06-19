<?php

namespace Tests\Feature\Api;

use App\Models\Organization;
use App\Models\User;

class ProPlanTest extends ApiTestCase
{
    public function test_owner_can_create_more_than_three_projects_on_pro_plan(): void
    {
        $organization = Organization::factory()->pro()->create();
        $owner = User::factory()->forOrganization($organization)->owner()->create();

        $this->actingAsApiUser($owner);

        for ($i = 1; $i <= 4; $i++) {
            $this->postJson('/api/projects', ['name' => "Project {$i}"])
                ->assertCreated();
        }

        $this->assertDatabaseCount('projects', 4);
    }

    public function test_member_can_create_more_than_three_projects_on_pro_plan(): void
    {
        $organization = Organization::factory()->pro()->create();
        $member = User::factory()->forOrganization($organization)->member()->create();

        $this->actingAsApiUser($member);

        for ($i = 1; $i <= 4; $i++) {
            $this->postJson('/api/projects', ['name' => "Project {$i}"])
                ->assertCreated();
        }

        $this->assertDatabaseCount('projects', 4);
    }
}
