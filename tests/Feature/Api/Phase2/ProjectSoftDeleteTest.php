<?php

namespace Tests\Feature\Api\Phase2;

use App\Models\Project;
use Tests\Feature\Api\ApiTestCase;

class ProjectSoftDeleteTest extends ApiTestCase
{
    public function test_soft_deleted_project_does_not_count_toward_free_plan_limit(): void
    {
        $user = $this->createOrganizationUser();

        $projects = Project::factory()->count(3)->create([
            'organization_id' => $user->organization_id,
        ]);

        $this->actingAsApiUser($user)
            ->deleteJson('/api/projects/'.$projects->first()->id)
            ->assertOk();

        $this->actingAsApiUser($user)
            ->postJson('/api/projects', ['name' => 'Replacement Project'])
            ->assertCreated();

        $this->assertDatabaseCount('projects', 4);
    }

    public function test_soft_deleted_project_is_excluded_from_list(): void
    {
        $user = $this->createOrganizationUser();

        $project = Project::factory()->create([
            'organization_id' => $user->organization_id,
        ]);

        $this->actingAsApiUser($user)
            ->deleteJson('/api/projects/'.$project->id)
            ->assertOk();

        $this->actingAsApiUser($user)
            ->getJson('/api/projects')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_soft_deleted_project_can_be_restored(): void
    {
        $user = $this->createOrganizationUser();

        $project = Project::factory()->create([
            'organization_id' => $user->organization_id,
            'name' => 'Archived Project',
        ]);

        $this->actingAsApiUser($user)
            ->deleteJson('/api/projects/'.$project->id)
            ->assertOk();

        $this->actingAsApiUser($user)
            ->postJson('/api/projects/'.$project->id.'/restore')
            ->assertOk()
            ->assertJsonPath('data.name', 'Archived Project');

        $this->actingAsApiUser($user)
            ->getJson('/api/projects')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
