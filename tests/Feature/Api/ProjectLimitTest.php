<?php

namespace Tests\Feature\Api;

use App\Models\Project;

class ProjectLimitTest extends ApiTestCase
{
    public function test_free_plan_blocks_fourth_project_with_402(): void
    {
        $user = $this->createOrganizationUser();

        Project::factory()->count(3)->create([
            'organization_id' => $user->organization_id,
        ]);

        $response = $this->actingAsApiUser($user)->postJson('/api/projects', [
            'name' => 'Fourth Project',
        ]);

        $response->assertStatus(402)
            ->assertJsonPath('message', 'Project limit reached for your current plan. Upgrade to Pro to create more projects.');

        $this->assertDatabaseCount('projects', 3);
    }
}
